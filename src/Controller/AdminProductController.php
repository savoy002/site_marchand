<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Product\Category;
use App\Entity\Product\Product;
use App\Entity\Product\VariantProduct;
use App\Form\Type\Product\CategoryType;
use App\Form\Type\Product\ProductType;
use App\Form\Type\Product\VariantProductType;
use App\Form\Type\Product\Manager\ManageCategoriesProductType;
use App\Form\Type\Product\Manager\ManageCategoriesVariantProductType;
use App\Form\Type\Product\Manager\ManageProductVariantProductType;
use App\Form\Type\Product\Manager\ManageProductsCategoryType;
use App\Form\Type\Product\Manager\ManageVariantsProductsProductType;
use App\Form\Type\Product\Manager\ManageVariantsProductsCategoryType;


class AdminProductController extends AbstractController
{

	//Le nombre Products ou VariantsProduct par page.
	const NUMBER_BY_PAGE = 5;

	private $filesSystem;

	public function __construct() {
		$this->filesSystem = new Filesystem();
	}

	/**
	 * @Route("/admin/menu/products", name="menu_product")
	 */
	public function menuProducts() 
	{
		return $this->render('admin/products/menu_product.html.twig');
	}

	//
	//Partie Category.
	//

	/**
     * @Route("/admin/categories", name="categories")
     */
    public function categories() 
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy(['delete' => false]);

        return $this->render('admin/products/categories/categories.html.twig', ['categories' => $categories]);
    }

    /**
     * @Route("/admin/category/{id}", name="category")
     */
    public function caterory($id) 
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findBy(['delete' => false, 'id' => $id]);
        if(empty($category))
        	return $this->redirectToRoute('categories');
        return $this->render('admin/products/categories/category.html.twig', ['category' => $category[0]]);
    }

    /**
     * @Route("/admin/categories/create", name="create_category")
     */
    public function createCategory(Request $request) 
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $errors = array();
        if($form->isSubmitted() && $form->isValid()) {
            $valid = true;
            //Création de l'attribut code si il n'a pas été crée par l'utilisateur.
            if($category->getCode() === null || $category->getCode() === "") {
                $code = str_replace(' ', '_', $category->getName());
                $category->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
            }
            //Vérification qu'aucune autre Category n'a le même code.
            $exist = $this->getDoctrine()->getRepository(Category::class)->findBy(['code' => $category->getCode()]);
            if(!empty($exist)) {
                $errors[] = "Le code de la catégorie est déjà utilisé par une autre catégorie.";
                $valid = false;
            }
            if($valid) {
            	//Ajout de l'image dans le dossier public/img/uploads/Product/Category .
                $image = $form->get('image')->getData();
                if($image) {
                    $res = $this->saveImage($image, 'category_image_directory');
                    if(gettype($res) === "string")
                    	$category->setImgFileName($res);
					else 
                    	return $this->render('admin/produts/categories/form_category.html.twig', 
                            ['form' => $form->createView(), 'errors' => $res, 'create' => true]);
                }
                $this->getDoctrine()->getManager()->persist($category);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute("categories");
            }
        }
        return $this->render('admin/products/categories/form_category.html.twig', 
            ['form' => $form->createView(), 'errors' => $errors, 'create' => true]);
    }

    /**
     * @Route("/admin/category/{id}/modify", name="modify_category")
     */
    public function modifyCategory(Request $request, $id) 
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $errors = array();
        if($form->isSubmitted() && $form->isValid()) {
            $valid = true;
            //Création de l'attribut code si il n'a pas été crée par l'utilisateur.
            if($category->getCode() === null || $category->getCode() === "") {
                $code = str_replace(' ', '_', $category->getName());
                $category->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
            }
            //Vérification qu'aucune autre catégorie n'a le même code.
            $exists = $this->getDoctrine()->getRepository(Category::class)->findBy(['code' => $category->getCode()]);
            if(!empty($exists)) {
            	$exit = false;
            	foreach($exists as $category_exist) {
            		if($category->getId() != $category_exist->getId()) 
            			$exist = true;
            	}
            	if($exist) {
            		$errors[] = "Le code de la catégorie existe déjà.";
                	$valid = false;
            	}
            }
            if($valid) {
            	//Suppression dans l'ancienne image si elle existe et ajout de la nouvelle image dans le dossier public/img/uploads/Product/Category .
                $image = $form->get('image')->getData();
                if($image) {
                    $res_save = $this->saveImage($image, 'category_image_directory');
                    if(gettype($res_save) === "string") {
                    	if($category->getImgFileName() != null) {
                    		$res_delete = $this->deleteImage($category->getImgFileName(), 'category_image_directory');
                    		if(!$res_delete)
                    			$category->setImgFileName($res_save);
                    		else 
                    			return $this->render('admin/produts/categories/form_category.html.twig', 
                        			['form' => $form->createView(), 'errors' => $e[$res_delete], 'create' => false]);
                    	} else {
                    		$category->setImgFileName($res_save);
                    	}
                    } else 
                    	return $this->render('admin/produts/categories/form_category.html.twig', 
                        	['form' => $form->createView(), 'errors' => $e[$res_save], 'create' => false]);
                }
                $this->getDoctrine()->getManager()->persist($category);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute("categories");
            }
        }
        return $this->render('admin/products/categories/form_category.html.twig', 
            ['form' => $form->createView(), 'errors' => $errors, 'create' => false, 'category_name' => $category->getName(), 
            'category_id' => $category->getId()]);
    }

	/**
	 * @Route("/admin/category/{id}/manageProducts", name="manage_products_category")
	 */
	public function manageProductsCategory(Request $request, $id) 
	{
		$category = $this->getDoctrine()->getRepository(Category::class)->findBy(['delete' => false, 'id' => $id])[0];
		//$former_products = $this->getDoctrine()->getRepository(Product::class)->findProductsByCategory($category);
		$form = $this->createForm(ManageProductsCategoryType::class, $category);
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			$manager = $this->getDoctrine()->getManager();
			//Décommenter les ligne suivantes si la Category et les Products ne se modifient pas.
			/*foreach($former_products as $product) {
				if(!$category->hasProduct($product)) {
					$product->removeCategory($category);
					$manager->persist($product);
				}
			}
			foreach($category->getProducts() as $new_product) {
				$contain = false;
				foreach($former_products as $product) {
					if($new_product->getId() === $product->getId())
						$contain = true;
				}
				if(!$contain) {
					$new_product->addCategory($category);
					$manager->persist($new_product);
				}
			}*/
			$manager->persist($category);
			$manager->flush();
			return $this->redirectToRoute('category', ['id' => $id]);
		}
		return $this->render('admin/products/categories/manage_products_category.html.twig', ['form' => $form->createView(), 'category' => $category]);
	}

	/**
	 * @Route("/admin/category/{id}/manageVariantsProducts", name="manage_variants_products_category")
	 */
	public function manageVarianstProductsCategory(Request $request, $id)
	{
		$category = $this->getDoctrine()->getRepository(Category::class)->findBy(['delete' => false, 'id' => $id])[0];
		//$former_variants = $this->getDoctrine()->getRepository(VariantProduct::class)->findVariantsProductsByCategory($category);
		$form = $this->createForm(ManageVariantsProductsCategoryType::class, $category);
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			$manager = $this->getDoctrine()->getManager();
			//Décommenter les ligne suivantes si la Category et les VariantsProducts ne se modifient pas.
			/*foreach($former_variants as $variant_product) {
				if(!$category->hasVariantProduct($variant_product)) {
					$variant_product->removeCategory($category);
					$manager->persist($variant_product);
				}
			}
			foreach($category->getProducts() as $new_variant) {
				$contain = false;
				foreach ($former_variants as $variant_product) {
					if($new_variant->getId() === $variant_product->getId())
						$contain = true;
				}
				if(!$contain) {
					$new_variant->addCategory($category);
					$manager->persist($new_variant);
				}
			}*/
			$manager->persist($category);
			$manager->flush();
			return $this->redirectToRoute('category', ['id' => $id]);
		}
		return $this->render('admin/products/categories/manage_variants_products_category.html.twig', 
			['form' => $form->createView(), 'category' => $category]);
	}

    /**
     * @Route("/admin/category/{id}/activate", name="activate_disactivate_category")
     */
    public function activateDisactivateCategory($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $category->setValid(!$category->getValid());
        $this->getDoctrine->getManager()->persist($category);
        $this->getDoctrine->getManager()->flush();
        return $this->redirectToRoute('category');
    }

    /**
     * @Route("/admin/category/{id}/delete", name="delete_category")
     */
    public function deleteCategory($id) 
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $category->setDelete(true);
        //Enlève les lien entre la Category et les Products.
        foreach($category->getProducts() as $product) {
        	$category->removeProduct($product);
        	$this->getDoctrine()->getManager()->persist($product);
        }
        //Enlève les lien entre la Category et les VariantsProducts.
        foreach ($category->getVariantsProducts as $variant_product) {
        	$category->removeVariantProduct($variant_product);
        	$this->getDoctrine()->getManager()->persist($variant_product);
        }
        $this->getDoctrine->getManager()->persist($category);
        $this->getDoctrine->getManager()->flush();
        return $this->redirectToRoute('categories');
    }

    //
    //Partie Product
    //

 	/**
 	 * @Route("/admin/products", name="products")
 	 */
    public function products(Request $request) 
    {
    	$errors = array();
    	$former_request = array();
    	//Place le nombre de Produts par page dans les paramètre de la recherche.
    	$criteria = ['number_by_page' => self::NUMBER_BY_PAGE];
    	//Recherche la demande de page de l'administrateur si elle existe.
    	$page = $request->request->get('page');

    	//Gestion de la sélection si il y an a une.
    	if($request->request->get('research') === "research") {
    		//Création des différents paramètres de la recherche.
    		if($request->request->get('name') != "" && $request->request->get('name') !== null) {
                $criteria['name'] = array('value' => $request->request->get('name'), 
                    'type' => $request->request->get('type_research_name'));
                $former_request['name'] = $request->request->get('name');
                $former_request['type_research_name'] = $request->request->get('type_research_name');
            }
            if($request->request->get('code') != "" && $request->request->get('code') !== null) {
                $criteria['code'] = array('value' => $request->request->get('code'), 
                    'type' => $request->request->get('type_research_code'));
                $former_request['code'] = $request->request->get('code');
                $former_request['type_research_code'] = $request->request->get('type_research_code');
            }
            if($request->request->get('stock') != "" && $request->request->get('stock') !== null) {
                $criteria['stock'] = array('value' => $request->request->get('stock'), 
                    'type' => $request->request->get('type_research_stock'));
                $former_request['stock'] = $request->request->get('stock');
                $former_request['type_research_stock'] = $request->request->get('type_research_stock');
            }
            if($request->request->get('description') != "" && $request->request->get('description') !== null) {
                $criteria['description'] = $request->request->get('description');
                $former_request['description'] =  $request->request->get('description');
            }
            if($request->request->get('activate') != "none" && $request->request->get('activate') !== null) {
                $criteria['activate'] = $request->request->get('activate');
                $former_request['activate'] =  $request->request->get('activate');
            }
            //Ajout des ordres de recherches.
            if($request->request->get('orderBy_sortBy') != "none" && $request->request->get('orderBy_sortBy') !== null) {
            	$criteria['orderBy'] = array('attribut' => $request->request->get('orderBy_sortBy'), 
                    'order' =>  $request->request->get('orderBy_sortDir'));
                $former_request['orderBy_sortBy'] = $request->request->get('orderBy_sortBy');
                $former_request['orderBy_sortDir'] = $request->request->get('orderBy_sortDir');
            }
            //Ajout du numéro de page.
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
                //Calcul le nombre de produits.
                $number_products = $this->getDoctrine()->getRepository(Product::class)
                    ->adminResearchNumberProducts($criteria)[0][1];
    	} else {
    		//Ajout le nombre de pages.
    		$criteria['page'] = 0;
            $page = 1;
            //Calcul le nombre de produits.
	    	$number_products = intval($this->getDoctrine()->getRepository(Product::class)->countNumberProducts()[0][1]);
    	}
    	//Cacul le nombre de pages.
    	$number_pages = 
            intval( $number_products / self::NUMBER_BY_PAGE ) + ( ( $number_products % self::NUMBER_BY_PAGE === 0 )?0:1 );
    	//Recherche les produits à retourner.
		$products = $this->getDoctrine()->getRepository(Product::class)->adminResearchProduct($criteria);

        return $this->render('admin/products/products/products.html.twig', 
        	['products' => $products, 'errors' => $errors, 'page' => $page, 'number_pages' => $number_pages, 
             'request' => $former_request]);
    }

    /**
     * @Route("/admin/product/{id}", name="product")
     */
    public function product($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->findBy(['delete' => false, 'id' => $id]);
        if(empty($product))
        	return $this->redirectToRoute('products');
        return $this->render('admin/products/products/product.html.twig', ['product' => $product[0]]);
    }

    /**
     * @Route("/admin/products/create", name="create_product")
     */
    public function createProduct(Request $request) 
    {
    	$product = new Product();
        $form = $this->createForm(ProductType::class, $product);
       	$form->handleRequest($request);
       	$errors = array();
       	if($form->isSubmitted() && $form->isValid()) {
       		$valid = true;
       		//Création de l'attribut code si il n'a pas été crée par l'utilisateur.
       		if($product->getCode() === null || $product->getCode() === '') {
       			$code = str_replace(' ', '_', $product->getName());
                $product->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
       		}
       		//Vérification qu'aucun autre Product n'a le même code.
       		$exist = $this->getDoctrine()->getRepository(Product::class)->findBy(['code' => $product->getCode()]);
       		if(!empty($exist))  {
       			$errors[] = "Le code du produit existe déjà.";
       			$valid = false;
       		}
       		if($valid) {
       			$image = $form->get('image')->getData();
       			//Ajout de l'image dans le dossier public/img/uploads/Product/Product .
                if($image) {
                    $res = $this->saveImage($image, 'product_image_directory');
                    if(gettype($res) === "string")
                    	$product->setImgFileName($res);
					else 
                    	return $this->render('admin/produts/products/form_product.html.twig', 
                            ['form' => $form->createView(), 'errors' => $res, 'create' => true]);
                }
                $product->setStock(0);
                $this->getDoctrine()->getManager()->persist($product);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute("products");
       		}
       	}
       	return $this->render('admin/products/products/form_product.html.twig', ['form' => $form->createView(), 'errors' => $errors, 'create' => true]);
    }

    /**
     * @Route("/admin/product/{id}/modify", name="modify_product")
     */
    public function modifyProduct(Request $request, $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $form = $this->createForm(productType::class, $product);
        $form->handleRequest($request);
        $errors = array();
        if($form->isSubmitted() && $form->isValid()) {
            $valid = true;
            //Création de l'attribut code si il n'a pas été crée par l'utilisateur.
            if($product->getCode() === null || $product->getCode() === "") {
                $code = str_replace(' ', '_', $product->getName());
                $product->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
            }
            //Vérification qu'aucun autre Product n'a le même code.
            $exists = $this->getDoctrine()->getRepository(Product::class)->findBy(['code' => $product->getCode()]);
            if(!empty($exists)) {
            	$exist = false;
            	foreach($exists as $product_exist) {
            		if($product->getId() != $product_exist->getId()) 
            			$exist = true;
            	}
            	if($exist) {
            		$errors[] = "Le code du produit déjà utilisé par un autre produit.";
                	$valid = false;
            	}
            }
            if($valid) {
                $image = $form->get('image')->getData();
                //Suppression dans l'ancienne image si elle existe et ajout de la nouvelle image dans le dossier public/img/uploads/Product/Product .
                if($image) {
                    $res_save = $this->saveImage($image, 'product_image_directory');
                    if(gettype($res_save) === "string") {
                    	if($product->getImgFileName() != null) {
                    		$res_delete = $this->deleteImage($product->getImgFileName(), 'product_image_directory');
                    		if(!$res_delete)
                    			$product->setImgFileName($res_save);
                    		else 
                    			return $this->render('admin/products/products/form_product.html.twig', 
                        			['form' => $form->createView(), 'errors' => $e[$res_delete], 'create' => false]);
                    	} else {
                    		$product->setImgFileName($res_save);
                    	}
                    } else 
                    	return $this->render('admin/products/products/form_product.html.twig', 
                        	['form' => $form->createView(), 'errors' => $e[$res_save], 'create' => false]);
                }
                $this->getDoctrine()->getManager()->persist($product);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute("product", ['id' => $product->getId()]);
            }
        }
        return $this->render('admin/products/products/form_product.html.twig', 
            ['form' => $form->createView(), 'errors' => $errors, 'create' => false, 'product_name' => $product->getName(), 
            	'product_id' => $product->getId()]);
    }

    /**
     * @Route("admin/product/{id}/manageCategories", name="manage_categories_product")
     */
    public function manageCategoriesProduct(Request $request, $id) {
    	$product = $this->getDoctrine()->getRepository(Product::class)->findBy(['delete' => false, 'id' => $id])[0];
    	$former_categories = $this->getDoctrine()->getRepository(Category::class)->findCategoriesByProduct($product);
    	$form = $this->createForm(ManageCategoriesProductType::class, $product);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid()) {
    		$manager = $this->getDoctrine()->getManager();
    		//Enlève le Product aux Categories enlevées.
    		foreach($former_categories as $category) {
    			if(!$product->hasCategory($category)) {
    				$category->removeProduct($product);
    				$manager->persist($category);
    			}
    		}
    		//Ajoute le Product aux Categories ajoutées.
    		foreach($product->getCategories() as $new_category) {
    			$contain = false;
    			foreach($former_categories as $category) {
    				if($new_category->getId() === $category->getId())
    					$contain = true;
    			}
    			if(!$contain) {
    				$new_category->addProduct($product);
    				$manager->persist($new_category);
    			}
    		}
    		$manager->persist($product);
    		$manager->flush();
    		return $this->redirectToRoute('product', ['id' => $id]);
    	}
    	return $this->render('admin/products/products/manage_categories_product.html.twig', ['form' => $form->createView(), 'product' => $product]);
    }

    /**
     * @Route("admin/product/{id}/manageVariantsProducts", name="manage_variants_products_product")
     */
    public function manageVariantsProductsProduct(Request $request, $id) {
    	$product = $this->getDoctrine()->getRepository(Product::class)->findBy(['delete' => false, 'id' => $id])[0];
    	$former_variants = $this->getDoctrine()->getRepository(VariantProduct::class)->findVariantsProductsByProduct($product);
    	$form = $this->createForm(ManageVariantsProductsProductType::class, $product);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid()) {
    		$manager = $this->getDoctrine()->getManager();
    		//Enlève le Product aux VariantsProducts enlevées.
    		foreach($former_variants as $variant_product) {
    			if(!$product->hasVariantProduct($variant_product)) {
    				$variant_product->setProduct(null);
    				$manager->persist($variant_product);
    			}
    		}
    		//Enlève le Product aux VariantsProducts ajoutées.
    		foreach($product->getVariantsProducts() as $new_variant) {
    			$contain = false;
    			foreach($former_variants as $variant_product) {
    				if($new_variant->getId() === $variant_product->getId())
    					$contain = true;
    			}
    			if(!$contain) {
    				$new_variant->setProduct($product);
    				$manager->persist($new_variant);
    			}
    		}
    		$product->calculStock();
    		$manager->persist($product);
    		$manager->flush();
    		return $this->redirectToRoute('product', ['id' =>  $id]);
    	}
    	return $this->render('admin/products/products/manage_variants_products_product.html.twig', 
    		['form' => $form->createView(), 'product' => $product]);
    }

    /**
     * @Route("/admin/product/{id}/activate", name="activate_disactivate_product")
     */
    public function activateDisactivateProduct($id)
    {
    	$product = $this->getDoctrine()->getRepository(Product::class)->findBy(['delete' => false, 'id' => $id]);
    	if(empty($product))
    		return $this->redirectToRoute("products");
    	$product = $product[0];
    	$product->setActivate(!$product->getActivate());
    	$this->getDoctrine()->getManager()->persist($product);
    	$this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('product', ['id' => $id]);
    }

    /**
     * @Route("/admin/product/{id}/delete", name="delete_product")
     */
    public function deleteProduct($id)
    {
    	$product = $this->getDoctrine()->getRepository(Product::class)->findBy(['delete' => false, 'id' => $id]);
    	if(empty($product))
    		return $this->redirectToRoute("products");
    	$product = $product[0];
    	$product->setDelete(true);
    	//Change le Product des VariantsProducts contenuent par le Product.
    	foreach($product->getVariantsProducts() as $variant_product) {
    		$product->removeVariantProduct($variant_product);
    		$this->getDoctrine()->getManager()->persist($variant_product);
    	}
    	//Enlève le Product des Categories contenuent par le Product.
    	foreach($product->getCategories() as $category) {
    		$product->removeCategory($category);
    		$this->getDoctrine()->getManager()->persist($category);
    	}
    	$this->getDoctrine()->getManager()->persist($product);
    	$this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('products');
    }

    //
    //Partie VariantProduct.
    //

	/**
 	 * @Route("/admin/variants_products", name="variants_products")
 	 */
    public function variantsProducts(Request $request)
    {
    	$errors = array();
    	$former_request = array();
    	$criteria = ['number_by_page' => self::NUMBER_BY_PAGE];
    	//Recherche la demande de page de l'administrateur si elle existe.
    	$page = $request->request->get('page');

    	//Gestion de la sélection si il y an a une.
    	if($request->request->get('research') === "research") {
    		//Création des différents paramètres de la recherche.
    		if($request->request->get('name') != "" && $request->request->get('name') !== null) {
                $criteria['name'] = array('value' => $request->request->get('name'), 
                    'type' => $request->request->get('type_research_name'));
                $former_request['name'] = $request->request->get('name');
                $former_request['type_research_name'] = $request->request->get('type_research_name');
            }
            if($request->request->get('code') != "" && $request->request->get('code') !== null) {
                $criteria['code'] = array('value' => $request->request->get('code'), 
                    'type' => $request->request->get('type_research_code'));
                $former_request['code'] = $request->request->get('code');
                $former_request['type_research_code'] = $request->request->get('type_research_code');
            }
            if($request->request->get('stock') != "" && $request->request->get('stock') !== null) {
                $criteria['stock'] = array('value' => $request->request->get('stock'), 
                    'type' => $request->request->get('type_research_stock'));
                $former_request['stock'] = $request->request->get('stock');
                $former_request['type_research_stock'] = $request->request->get('type_research_stock');
            }
            if($request->request->get('price') != "" && $request->request->get('price') !== null) {
            	$criteria['price'] = array('value' => intval($request->request->get('price') * 100 ), 
                    'type' => $request->request->get('type_research_price'));
                $former_request['price'] = $request->request->get('price');
                $former_request['type_research_price'] = $request->request->get('type_research_price');
            }
            if($request->request->get('description') != "" && $request->request->get('description') !== null) {
                $criteria['description'] = $request->request->get('description');
                $former_request['description'] =  $request->request->get('description');
            }
            if($request->request->get('activate') != "none" && $request->request->get('activate') !== null) {
                $criteria['activate'] = $request->request->get('activate');
                $former_request['activate'] =  $request->request->get('activate');
            }
            //Ajout des ordres de recherches.
            if($request->request->get('orderBy_sortBy') != "none" && $request->request->get('orderBy_sortBy') !== null) {
            	$criteria['orderBy'] = 
                    array('attribut' => $request->request->get('orderBy_sortBy'), 'order' =>  $request->request->get('orderBy_sortDir'));
                $former_request['orderBy_sortBy'] = $request->request->get('orderBy_sortBy');
                $former_request['orderBy_sortDir'] = $request->request->get('orderBy_sortDir');
            }

            //Calcul le nombre de VariantsProduts.
            $number_products = $this->getDoctrine()->getRepository(VariantProduct::class)
                                    ->adminResearchNumberVariantsProducts($criteria)[0][1];
            //Recherche les produits à retourner.
            $number_pages = 
                    intval( $number_products / self::NUMBER_BY_PAGE ) + ( ( $number_products % self::NUMBER_BY_PAGE === 0 )?0:1 );
            //Ajout du numéro de page.
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
            
    	} else {
    		//Ajout du numéro de page.
    		$page = 1;
    		$criteria['page'] = 0;
    		//Calcul le nombre de VariantsProduts.
    		$number_products = 
                        intval($this->getDoctrine()->getRepository(VariantProduct::class)->adminCountNumberVariantsProducts()[0][1]);
            //Cacul le nombre de pages.
            $number_pages = 
                intval( $number_products / self::NUMBER_BY_PAGE ) + ( ( $number_products % self::NUMBER_BY_PAGE === 0 )?0:1 );
    	}
    	//Recherche les produits à retourner.
    	$variants_products = $this->getDoctrine()->getRepository(VariantProduct::class)->adminResearchVariantProduct($criteria);

        return $this->render('admin/products/variants_products/variants_products.html.twig', 
        	['variants_products' => $variants_products, 'errors' => $errors, 'request' => $former_request, 'page' => $page, 
        	'number_pages' => $number_pages]);
    }

	/**
 	 * @Route("/admin/variant_product/{id}", name="variant_product")
 	 */
    public function variantProduct($id)
    {
        $variant_product = $this->getDoctrine()->getRepository(VariantProduct::class)->findBy(['delete' => false, 'id' => $id]);
        if(empty($variant_product))
        	return $this->redirectToRoute('variants_products');
        return $this->render('admin/products/variants_products/variant_product.html.twig', ['variant_product' => $variant_product[0]]);
    }

    /**
     * @Route("/admin/variants_products/create", name="create_variant_product")
     */
    public function createVariantProduct(Request $request)
    {
        $variant_product = new VariantProduct();
        $form = $this->createForm(VariantProductType::class, $variant_product);
        $form->handleRequest($request);
        $errors = array();
        if($form->isSubmitted() && $form->isValid()) {
        	$valid = true;
        	//Création de l'attribut code si il n'a pas été crée par l'utilisateur.
        	if($variant_product->getCode() === null || $variant_product->getCode() === '') {
       			$code = str_replace(' ', '_', $variant_product->getName());
                $variant_product->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
       		}
       		//Vérification qu'aucune autre VariantProduct n'a le même code.
       		$exist = $this->getDoctrine()->getRepository(Product::class)->findBy(['code' => $variant_product->getCode()]);
       		if(!empty($exist))  {
       			$errors[] = "Le code du produit existe déjà.";
       			$valid = false;
       		}
       		if($valid) {
       			$image = $form->get('image')->getData();
       			//Ajout de l'image dans le dossier public/img/uploads/Product/VariantProduct .
                if($image) {
                    $res = $this->saveImage($image, 'variant_product_image_directory');
                    if(gettype($res) === "string")
                    	$variant_product->setImgFileName($res);
					else 
                    	return $this->render('admin/produts/variants_products/form_variant_product.html.twig', 
                            ['form' => $form->createView(), 'errors' => $res, 'create' => true]);
                }
                $variant_product->setStock(0);
                $this->getDoctrine()->getManager()->persist($variant_product->getProduct());
                $this->getDoctrine()->getManager()->persist($variant_product);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute("variants_products");
       		}
        }
        return $this->render('admin/products/variants_products/form_variant_product.html.twig', 
        	['form' => $form->createView(), 'errors' => $errors, 'create' => true]);
    }

	/**
     * @Route("/admin/variant_product/{id}/modify", name="modify_variant_product")
     */
    public function modifyVariantProduct(Request $request, $id)
    {
        $variant_product = $this->getDoctrine()->getRepository(VariantProduct::class)->find($id);
        $former_product = $this->getDoctrine()->getRepository(Product::class)->findProductByVariantProduct($variant_product)[0];
        $form = $this->createForm(VariantProductType::class, $variant_product);
        $form->handleRequest($request);
        $errors = array();
        if($form->isSubmitted() && $form->isValid()) {
            $valid = true;
        	//Création de l'attribut code si il n'a pas été crée par l'utilisateur.
            if($variant_product->getCode() === null || $variant_product->getCode() === "") {
                $code = str_replace(' ', '_', $variant_product->getName());
                $variant_product->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
            }
            //Vérification qu'aucune autre VariantProduct n'a le même code.
            $exists = $this->getDoctrine()->getRepository(VariantProduct::class)->findBy(['code' => $variant_product->getCode()]);
            if(!empty($exists)) {
            	$exist = false;
            	foreach($exists as $variant_product_exist) {
            		if($variant_product->getId() != $variant_product_exist->getId()) 
            			$exist = true;
            	}
            	if($exist) {
            		$errors[] = "Le code du produit déjà utilisé par un autre produit.";
                	$valid = false;
            	}
            }
            if($valid) {
                $image = $form->get('image')->getData();
                //Suppression dans l'ancienne image si elle existe et ajout de la nouvelle image dans le dossier 
                // public/img/uploads/Product/VariantProduct.
                if($image) {
                    $res_save = $this->saveImage($image, 'variant_product_image_directory');
                    if(gettype($res_save) === "string") {
                    	if($variant_product->getImgFileName() != null) {
                    		$res_delete = $this->deleteImage($variant_product->getImgFileName(), 'variant_product_image_directory');
                    		if(!$res_delete)
                    			$variant_product->setImgFileName($res_save);
                    		else 
                    			return $this->render('admin/products/variants_products/form_variant_product.html.twig', 
                        			['form' => $form->createView(), 'errors' => $e[$res_delete], 'create' => false]);
                    	} else {
                    		$variant_product->setImgFileName($res_save);
                    	}
                    } else 
                    	return $this->render('admin/products/variants_products/form_variant_product.html.twig', 
                        	['form' => $form->createView(), 'errors' => $e[$res_save], 'create' => false]);
                }
                if($former_product != null) {
                	if($former_product->getId() != $variant_product->getProduct()->getId())
			        	$this->getDoctrine()->getManager()->persist($former_product);
                }
                $this->getDoctrine()->getManager()->persist($variant_product->getProduct());
                $this->getDoctrine()->getManager()->persist($variant_product);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute("variant_product", ['id' => $variant_product->getId()]);
            }
        }
        return $this->render('admin/products/variants_products/form_variant_product.html.twig', 
            ['form' => $form->createView(), 'errors' => $errors, 'create' => false, 'variant_product_name' => $variant_product->getName(), 
            	'variant_product_id' => $variant_product->getId()]);
    }

    /**
     * @Route("admin/variant_product/{id}/manageCategories", name="manage_categories_variant_product")
     */
    public function manageCategoriesVariantProduct(Request $request, $id)
    {
    	$variant_product = $this->getDoctrine()->getRepository(VariantProduct::class)->findBy(['delete' => false, 'id' => $id])[0];
    	$former_categories = $this->getDoctrine()->getRepository(Category::class)->findCategoriesByVariantProduct($variant_product);
    	$form = $this->createForm(ManageCategoriesVariantProductType::class, $variant_product);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid()) {
    		$manager = $this->getDoctrine()->getManager();
    		//Enlève la VariantProduct aux Categories enlevées.
    		foreach($former_categories as $category) {
    			if(!$variant_product->hasCategory($category)) {
    				$category->removeVariantProduct($variant_product);
    				$manager->persist($category);
    			}
    		}
    		//Ajoute la VariantProduct aux Categories ajoutées.
    		foreach($variant_product->getCategories() as $new_category) {
    			$contain = false;
    			foreach($former_categories as $category) {
    				if($new_category->getId() === $category->getId())
    					$contain = true;
    			}
    			if(!$contain) {
    				$new_category->addVariantProduct($variant_product);
    				$manager->persist($new_category);
    			}
    		}
    		$manager->persist($variant_product);
    		$manager->flush();
    		return $this->redirectToRoute('variant_product', ['id' => $id]);
    	}
    	return $this->render('admin/products/variants_products/manage_categories_variant_product.html.twig', 
    		['form' => $form->createView(), 'variant_product' => $variant_product]);
    }

    /**
     * @Route("admin/variant_product/{id}/manageProducts", name="manage_products_variant_product")
     */
    public function manageProductsVariantProduct(Request $request, $id)
    {
    	$variant_product = $this->getDoctrine()->getRepository(VariantProduct::class)->findBy(['delete' => false, 'id' => $id])[0];
    	//Regarde si la VariantProduct a un ancien Product.
    	$former_product = $this->getDoctrine()->getRepository(Product::class)->findProductByVariantProduct($variant_product);
    	if(!empty($former_products)) 
    		$former_product = $former_product[0];
    	else 
    		$former_product = null;
    	$form = $this->createForm(ManageProductVariantProductType::class, $variant_product);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid()) {
			//$this->getDoctrine()->getManager()->persist($variant_product->getProduct());
			//Enlève la VariantProduct de l'ancien Product.
    		if($former_product !== null){
    			$former_product->calculStock();
    			$this->getDoctrine()->getManager()->persist($former_product);
    		}
    		$variant_product->getProduct()->calculStock();
    		$this->getDoctrine()->getManager()->persist($variant_product);
    		$this->getDoctrine()->getManager()->flush();
			return $this->redirectToRoute('variant_product', ['id' => $id]);
    	}
    	return $this->render('admin/products/variants_products/manage_product_variant_product.html.twig',
    		['form' => $form->createView(), 'variant_product' => $variant_product]);
    }

    /**
     * @Route("/admin/variant_product/{id}/activity", name="activate_disactivate_variant_product")
     */
    public function activateDisactivateVariantProduct($id)
    {
        $variant_product = $this->getDoctrine()->getRepository(VariantProduct::class)->findBy(['delete' => false, 'id' => $id]);
        if(empty($variant_product))
        	return $this->redirectToRoute('variants_products');
        $variant_product = $variant_product[0];
        $variant_product->setActivate(!$variant_product->getActivate());
        $this->getDoctrine()->getManager()->persist($variant_product);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('variant_product', ['id' => $id]);
    }

    /**
     * @Route("/admin/variant_product/{id}/delete", name="delete_variant_product")
     */
    public function deleteVariantProduct($id)
    {
        $variant_product = $this->getDoctrine()->getRepository(VariantProduct::class)->findBy(['delete' => false, 'id' => $id]);
        if(empty($variant_product))
        	return $this->redirectToRoute('variants_products');
        $variant_product = $variant_product[0];
        $variant_product->setDelete(true);
        //Enlève la VariantProduct au Product contenu par la VariantProduct.
        if($variant_product->getProduct() !== null) {
        	$product = $this->getDoctrine()->getRepository(Product::class)->findBy(['delete' => false, 'id' => $variant_product->getProduct()->getId()]);
        	$variant_product->setProduct(null);
        	$product->calculStock();
        	$this->getDoctrine()->getManager()->persist($product);
        }
        //Enlève la VariantProduct des Categories contenu par la VariantProduct.
        foreach($variant_product->getCategories() as $category) {
        	$variant_product->removeCategory($category);
        	$this->getDoctrine()->getManager()->persist($category);
        }
        $this->getDoctrine()->getManager()->persist($variant_product);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('variants_products');
    }

    //
    //Les méthodes pour sauvegarder et supprimer les images.
    //

    public function saveImage($image, $parameter_directory) {
    	$originalImagename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeImageName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalImagename);
        $newImagename = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();
        try {
            $image->move($this->getParameter($parameter_directory), $newImagename);
            return $newImagename;
        } catch(FileException $e) {
            return $e;
        }
    }

    public function deleteImage($nameImage, $parameter_directory) {
    	try {
            $this->filesSystem->remove($this->getParameter($parameter_directory).'/'.$nameImage);
            return false;
        } catch( IOExceptionInterface $e) {
            return $e;
        }
    }

}
