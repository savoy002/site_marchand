<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Command\Address;
use App\Entity\Command\Command;
use App\Entity\Command\PieceCommand;
use App\Entity\Command\TypeDelivery;
use App\Entity\Command\typeDeliverySelected;
use App\Entity\Product\Category;
use App\Entity\Product\Product;
use App\Entity\Product\VariantProduct;
use App\Entity\User\Comment;
use App\Entity\User\User;

use App\Form\Type\User\CommentType;
use App\Form\Type\Command\AddProductCommandType;
use App\Form\Type\Command\ChangeAddressType;
use App\Form\Type\Command\SelectTypeDeliveryType;


class StoreController extends AbstractController
{

    //Le nombre de produits par page.
    //Attention si vous changez la valeur de cette constante pensez aussi à changer celle des tests.
    const NUMBER_PRODUCTS_BY_PAGE = 6;

    /**
     * @Route("/", name="store")
     */
    public function index()
    {
        $var_products = $this->getDoctrine()->getRepository(VariantProduct::class)
            ->findBy(['delete' => false, 'activate' => true, 'isWellcome' => true]);
        
        return $this->render('store/wellcome_page.html.twig', ['products' => $var_products, 'basket' => $this->getBasket()]);
    }

    /**
     * @Route("/store/test", name="store_test")
     */
    public function test()
    {
        //$this->container->get('security.password_encoder');
        return $this->render('store/index.html.twig', [
            'controller_name' => 'StoreController/test',
        ]);
    }

    /**
     * @Route("/store/products", name="store_products")
     */
    public function showProducts(Request $request)
    {
        $criteria = array();
        $criteria['number_by_page'] = self::NUMBER_PRODUCTS_BY_PAGE;
        $former_request = array();

        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy(['activate' => true, 'delete' => false]);
        $products = $this->getDoctrine()->getRepository(Product::class)->findBy(['activate' => true, 'delete' => false]);

        $page = $request->request->get('page');

        //Gestion de la recheche.
        if($request->request->get('research') === "research") {

            //Ajoute les catégories choisis par l'utilisateur
            $choice_categories = array();
            foreach ($categories as $category) {
                if($request->request->get('category_'.$category->getId()) != "" 
                    && $request->request->get('category_'.$category->getId()) !== null)
                    $choice_categories[] = $category->getId();
            }
            if(!empty($choice_categories)) {
                $criteria['categories'] = $choice_categories;
                foreach ($choice_categories as $category_id)
                    $former_request['category_'.$category_id] = $category_id;
            }

            //Ajoute les produits choisis par l'utilisateur
            $choice_products = array();
            foreach ($products as $product) {
                if($request->request->get('product_'.$product->getId()) != ""
                    && $request->request->get('product_'.$product->getId()) !== null)
                    $choice_products[] = $product->getId();
            }
            if(!empty($choice_products)) {
                $criteria['products'] = $choice_products;
                foreach($choice_products as $product_id)
                    $former_request['product_'.$product_id] = $product_id;
            }

        }

        //Calcul le nombre de VariantProducts et de pages
        $number_var_products = $this->getDoctrine()->getRepository(VariantProduct::class)
            ->storeResearchNumberVariantProduct($criteria)[0][1];
        $number_pages = intval( $number_var_products / self::NUMBER_PRODUCTS_BY_PAGE ) + 
            ( ( $number_var_products % self::NUMBER_PRODUCTS_BY_PAGE === 0 )?0:1 );

        //Ajoute le numéro de page
        if($page != "" && $page !== null) {
            if($page === 'Début') {
                $criteria['page'] = 0;
                $page = 1;
            } else if($page === 'Fin') {
                $criteria['page'] = $number_pages - 1;
                $page = $number_pages;
            } else {
                $criteria['page'] = intval($page) - 1;
            }
        } else 
            $page = 1;

        //Recherche les VariantProducts à retourner.
        $var_products = $this->getDoctrine()->getRepository(VariantProduct::class)
            ->storeResearchVariantProduct($criteria);
        
        return $this->render('store/variants_products/show_products.html.twig', 
            [ 'var_products' => $var_products, 'categories' => $categories, 'products' => $products, 'number' => $number_var_products,
              'page' => $page, 'number_pages' => $number_pages, 'former_request' => $former_request, 'basket' => $this->getBasket() ]);
    }

    /**
     * @Route("/store/product/{code}", name="store_product")
     */
    public function showProduct(Request $request, $code)
    {
        $var_product = $this->getDoctrine()->getRepository(VariantProduct::class)
            ->findOneBy(['delete' => false, 'activate' => true, 'code' => $code]);

        if(is_null($var_product))
            return $this->redirectToRoute('store');

        //Gestion du formulaire pour les ajouter au panier.
        if($var_product->getStock() > 0) {
            //Création du formulaire.
            $piece_command = new PieceCommand();
            $form_command = $this->createForm(AddProductCommandType::class, $piece_command, ['stock' => $var_product->getStock()]);
            $form_command->handleRequest($request);
            if($form_command->isSubmitted() && $form_command->isValid()) {
                //Vérifie si l'utilisateur est connecté.
                if(is_null($this->getUser()))
                    return $this->redirectToRoute('app_login');
                //Vérifie que l'utilisateur a été vérifié.
                if(!$this->getUser()->getValid())
                    return $this->redirectToRoute('store_user');
                //Vérifie si il y a déjà une commande en cours.
                foreach($this->getUser()->getCommands() as $command) {
                    if($command->getIsBasket())
                        $basket = $command;
                }
                if(!isset($basket)) {
                    $basket = new Command();
                    $basket->setUser($this->getUser());
                    $this->getDoctrine()->getManager()->persist($this->getUser());
                }
                //Vérifie si la commande possède déjà le produit.
                // ------ Partie à vérifier. -----
                foreach ($basket->getProducts() as $piece) {
                    if($piece->getProduct() === $var_product)
                        $former_piece_product = $piece;
                }
                //Modifie le nombre de produits dans la commande si elle existe ou ajoute le produit dans la commande sinon. 
                if(isset($former_piece_product)) {
                    $former_piece_product->setPriceProduct($var_product->getPrice());
                    $former_piece_product->setNbProducts($piece_command->getNbProducts());
                    $this->getDoctrine()->getManager()->persist($former_piece_product);
                } else {
                    $piece_command->setPriceProduct($var_product->getPrice());
                    $piece_command->setProduct($var_product);
                    $piece_command->setCommand($basket);
                    $this->getDoctrine()->getManager()->persist($var_product);
                    $this->getDoctrine()->getManager()->persist($basket);
                    $this->getDoctrine()->getManager()->persist($piece_command);
                }

                //Penser à gérer les stockes dans cette méthode ou dans la validation de la commande.

                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('store_basket_article');
            }
        }
        if(!is_null($this->getUser())) {
            if($this->getUser()->didBuyProduct($var_product) && !$this->getUser()->isAlreadyComment($var_product)) {
                $comment = new Comment();
                $form_comment = $this->createForm(CommentType::class, $comment);
                $form_comment->handleRequest($request);
                if($form_comment->isSubmitted() && $form_comment->isValid()) {
                    $this->getUser()->addComment($comment);
                    $var_product->addComment($comment);
                    $this->getDoctrine()->getManager()->persist($comment);
                    $this->getDoctrine()->getManager()->persist($this->getUser());
                    $this->getDoctrine()->getManager()->flush();
                    $form_comment = null;
                }
            }
        }
        return $this->render('store/variants_products/show_product.html.twig', 
            ['product' => $var_product, 'form_command' => (isset($form_command))?($form_command->createView()):(null), 
             'form_comment' => (isset($form_comment) && !is_null($form_comment))?($form_comment->createView()):(null), 
             'basket' => $this->getBasket()]);
    }

    /**
     * @Route("/store/basket/article", name="store_basket_article")
     */
    public function basketShowArticle()
    {
        if($this->getBasket() === null)
            return $this->redirectToRoute('store');

        return $this->render('store/basket/basket_show_articles.html.twig', ['basket' => $this->getBasket()]);
    }

    /**
     * @Route("/store/basket/address", name="store_basket_address")
     */
    public function basketChoiceAddressDel(Request $request)
    {
        if($this->getBasket() !== null) {
            if(!$this->getBasket()->isEmptyProduct()) {
                // --- Penser à remettre l'adresse déjà enregistrée dans le formulaire. ---
                $address = new Address();
                $form = $this->createForm(ChangeAddressType::class, $address);
                $user_address = $this->getUser()->getLive();
                $errors = array();
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid() || $request->request->get("user_address") === "user_address") {
                    $former_address = $this->getBasket()->getPlaceDel();
                    //Regarde si l'utilisateur  ademandé d'utiliser l'adresse du compte.
                    if($request->request->get("user_address") === "user_address") {
                        $this->getBasket()->setPlaceDel($user_address);
                    } else {
                        //Regarde si le code postale est valide.
                        if(strlen($form->getData()->getZipCode()) != 5) {
                            if(strlen($form->getData()->get('zipCode')) === 4) 
                                $address->setZipCode("0".$address->getZipCode());
                            else
                                return $this->render('store/basket/basket_choice_address.html.twig', 
                                    ['form' => $form->createView(), 'errors' => $errors]);
                        }
                        $former_address = $this->getBasket()->getPlaceDel();
                        $this->getBasket()->setPlaceDel($address);
                    }
                    //Gère la mémoire de l'ancienne adresse si elle existe.
                    if($former_address !== null) {
                        $former_address->removeCommand($this->getBasket());
                        if($former_address->isEmptyCommand() && $former_address->isEmptyBelong())
                            $former_address->setDelete(true);
                        $this->getDoctrine()->getManager()->persist($former_address);
                    }
                    //Gère les sauvegardes des adresses et du panier.
                    $this->getDoctrine()->getManager()->persist($this->getBasket());
                    if($request->request->get("user_address") === "user_address")
                        $this->getDoctrine()->getManager()->persist($user_address);
                    else
                        $this->getDoctrine()->getManager()->persist($address);
                    //var_dump(is_null($this->getBasket()->getPlaceDel()));
                    //die();
                    $this->getDoctrine()->getManager()->flush();

                    return $this->redirectToRoute('store_basket_delivery');
                }
                return $this->render('store/basket/basket_choice_address.html.twig', 
                    ['form' => $form->createView(), 'basket' => $this->getBasket(), 'user_address' => $user_address]);
            }
        }
        
        return $this->redirectToRoute('store_products');
    }

    /**
     * @Route("/store/basket/delivery", name="store_basket_delivery")
     */
    public function basketSelectDelivery(Request $request)
    {
        if($this->getBasket() !== null) {
            if(!$this->getBasket()->isEmptyProduct() && $this->getBasket()->getPlaceDel() !== null) {
                if(is_null($this->getBasket()->getTypeDelSelected())){
                    $typeDeliverySelected = new typeDeliverySelected();  
                    $typeDeliverySelected->setCommand($this->getBasket());
                } else
                    $typeDeliverySelected = $this->getBasket()->getTypeDelSelected();
                $form = $this->createForm(SelectTypeDeliveryType::class, $typeDeliverySelected, 
                    ['zip_code' => $this->getBasket()->getPlaceDel()->getZipCode()]);
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid()) {
                    $typeDeliverySelected->setPriceDelivery($typeDeliverySelected->getTypeDelivery()->getPrice());
                    $this->getDoctrine()->getManager()->persist($typeDeliverySelected->getTypeDelivery());
                    $this->getDoctrine()->getManager()->persist($typeDeliverySelected);
                    $this->getDoctrine()->getManager()->persist($this->getBasket());
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('store_basket_payment');
                }
                return $this->render('store/basket/basket_select_type_delivery.html.twig', 
                    ['form' => $form->createView(), 'basket' => $this->getBasket()]);
            }
        }
        return $this->redirectToRoute('store_products');
    }

    /**
     * @Route("/store/basket/payment", name="store_basket_payment")
     */
    public function basketSelectPayment(Request $request)
    {

        return $this->redirectToRoute('store_test');
    }

    
    protected function getBasket()
    {
        return ($this->getUser() !== null)?($this->getUser()->getBasket()):null;
    }

}
