<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\Product\Category;
use App\Entity\Product\Product;
use App\Entity\Product\VariantProduct;
use App\Entity\User\User;
use App\Form\Type\User\UserType;
use App\Form\Type\Product\CategoryType;



class AdminController extends AbstractController
{

    const NUMBER_BY_PAGE = 5;

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/users", name="users")
     */
    public function users(Request $request)
    {
        $page = $request->request->get('page');
        $former_request = array();
        $errors = array();
        $criteria = ['number_by_page' => self::NUMBER_BY_PAGE];

        $number_users = intval($this->getDoctrine()->getRepository(User::class)->countNumberUsers()[0][1]);
        $number_pages = intval( $number_users / self::NUMBER_BY_PAGE ) + ( ( $number_users % self::NUMBER_BY_PAGE === 0 )?0:1 );

        if($request->request->get('research') === "research") {
            if($request->request->get('username') != "" && $request->request->get('username') !== null) {
                $criteria['username'] = array('value' => $request->request->get('username'), 
                    'type' => $request->request->get('type_research_username'));
                $former_request['username'] = $request->request->get('username');
                $former_request['type_research_username'] = $request->request->get('type_research_username');
            }
            if($request->request->get('email') != "" && $request->request->get('email') !== null) {
                $criteria['email'] = array('value' => $request->request->get('email'),
                    'type' => $request->request->get('type_research_email'));
                $former_request['email'] = $request->request->get('email');
                $former_request['type_research_email'] = $request->request->get('type_research_email');
            }
            if($request->request->get('roles') != "" && $request->request->get('roles') !== null) {
                $criteria['roles'] = $request->request->get('roles');
                $former_request['roles'] = $request->request->get('roles');
            }
            if($request->request->get('valid') != "" && $request->request->get('valid') !== null) {
                $criteria['valid'] = $request->request->get('valid');
                $former_request['valid'] =  $request->request->get('valid');
            }
            if($request->request->get('createdBy') != "" && $request->request->get('createdBy') !== null) {
                $criteria['createdBy'] = $request->request->get('createdBy');
                $former_request['createdBy'] =  $request->request->get('createdBy');
            }

            if($request->request->get('createdBefore') != "" && $request->request->get('createdBefore') !== null 
             &&  $request->request->get('createdAfter') != "" && $request->request->get('createdAfter') !== null) {
                if($request->request->get('createdBefore') >= $request->request->get('createdAfter')) {
                    $criteria['createdBefore'] = $request->request->get('createdBefore');
                    $former_request['createdBefore'] =  $request->request->get('createdBefore');
                    $criteria['createdAfter'] = $request->request->get('createdAfter');
                    $former_request['createdAfter'] =  $request->request->get('createdAfter');
                } else {
                    $errors[] = "La date d'avant la création ne peut pas être inférieur à la date d'après création.";
                }
            } else {
                if($request->request->get('createdBefore') != "" && $request->request->get('createdBefore') !== null) {
                    $criteria['createdBefore'] = $request->request->get('createdBefore');
                    $former_request['createdBefore'] =  $request->request->get('createdBefore');
                }
                if($request->request->get('createdAfter') != "" && $request->request->get('createdAfter') !== null) {
                    $criteria['createdAfter'] = $request->request->get('createdAfter');
                    $former_request['createdAfter'] =  $request->request->get('createdAfter');
                }
            }

            if($request->request->get('orderBy_sortBy') != "" && $request->request->get('orderBy_sortBy') !== null) {
                $criteria['orderBy'] = 
                    array('attribut' => $request->request->get('orderBy_sortBy'), 'order' =>  $request->request->get('orderBy_sortDir'));
                $former_request['orderBy_sortBy'] = $request->request->get('orderBy_sortBy');
                $former_request['orderBy_sortDir'] = $request->request->get('orderBy_sortDir');
            }

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

            $users = $this->getDoctrine()->getRepository(User::class)->adminResearchUser($criteria);
        } else {
            $criteria['page'] = 0;
            $page = 1;
            $users = $this->getDoctrine()->getRepository(User::class)->adminResearchUser($criteria);
        }

    	return $this->render('admin/users/users.html.twig', ['users' => $users, 'number_pages' => $number_pages, 'page' => $page, 
            'request' => $former_request, 'errors' => $errors]);
    }

    /**
     * @Route("/admin/user/{id}/", name="user")
     */
    public function showUser($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy( ['id' => $id, 'delete' => false] );
        return $this->render('admin/users/user.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/admin/users/create_admin_user/", name="admin_create_user")
     */
    public function createAdminUser(Request $request) 
    {
        if($this->getUser()->getSuperAdmin()) {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            $errors = array();
            if($form->isSubmitted() && $form->isValid()) {
                $valid = true;
                $exist = $this->getDoctrine()->getRepository(User::class)->findBy(['username' => $user->getUsername()]);
                if(!empty($exist)) {
                    $errors[] = "Le nom d'utilisateur existe déjà.";
                    $valid = false;
                }
                if($user->getPassword() != $form->get('verifyPassword')->getData()) {
                    $errors[] = "La vérification du mot de passe et le mot de passe sont différents.";
                    $valid = false;
                }
                if($valid) {
                    $user->setAdmin(true);
                    $user->setValid(true);
                    $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute("users");
                }
            }
            return $this->render('admin/users/create_user.html.twig', ['form' => $form->createView(), 'errors' => $errors]);
        }
        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/users/{id}/valid", name="valid_user")
     */
    public function userValid($id)
    {
        if($this->getUser()->getSuperAdmin()) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);
            $user->setValid(!$user->getValid());
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
        }
    	return $this->redirectToRoute('user', ['id' => $id]);
    }

    /**
     * @Route("/admin/users/{id}/delete", name="delete_user")
     */
    public function userDelete($id) 
    {
        if($this->getUser()->getSuperAdmin()) {
        	$user = $this->getDoctrine()->getRepository(User::class)->find($id);
        	if(!$user->getSuperAdmin()) {
        		$user->setDelete(true);
    	    	$this->getDoctrine()->getManager()->persist($user);
    	    	$this->getDoctrine()->getManager()->flush();
        	}
        }
    	return $this->redirectToRoute('users');
    }

    /**
     * @Route("/admin/categories", name="categories")
     */
    public function categories(Request $request) 
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('admin/products/categories/categories.html.twig', ['categories' => $categories]);
    }

    /**
     * @Route("/admin/category/{id}", name="category")
     */
    public function caterory($id) 
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        return $this->render('admin/products/categories/category.html.twig', ['category' => $category]);
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
            if($category->getCode() === null || $category->getCode() === "") {
                $code = str_replace(' ', '_', $category->getName());
                $category->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
            }
            var_dump($category->getCode());
            die();
            $exist = $this->getDoctrine()->getRepository(Category::class)->findBy(['code' => $category->getCode()]);
            if(!empty($exist)) {
                $errors[] = "Le code de la catégorie existe déjà.";
                $valid = false;
            }
            if($valid) {
                $image = $form->get('image')->getData();
                if($image) {
                    $originalImagename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeImageName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalImagename);
                    $newImagename = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();
                    try {
                        $image->move($this->getParameter('category_image_directory'), $newImagename);
                    } catch(FileException $e) {
                        return $this->render('store/produts/categories/create_category.html.twig', ['form' => $form->createView(), 'errors' => $e]);
                    }
                }
                $this->getDoctrine()->getManager()->persist($category);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute("categories");
            }
        }
        return $this->render('admin/products/categories/create_category.html.twig', ['form' => $form->createView(), 'errors' => $errors]);
    }

    /**
     * @Route("/admin/category/{id}/modify", name="modify_category")
     */
    public function modifyCategory(Request $request, $id) 
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/category/{id}/activate", name="activate_disactivate_category")
     */
    public function activateDisactivateCategory($id)
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/category/{id}/delete", name="delete_category")
     */
    public function deleteCategory($id) 
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /*public function products(Request $request) 
    {
        return null;
    }

    public function product()
    {
        return null;
    }

    public function createProduct(Request $request) 
    {
        return null;
    }

    public function modifyProduct(Request $request, $id)
    {
        return null;
    }

    public function activateDisactivateProduct($id)
    {
        return null;
    }

    public function deleteProduct($id)
    {
        return null;
    }

    public function variantsProducts(Request $request)
    {
        return null;
    }

    public function variantProduct()
    {
        return null;
    }

    public function createVariantProduct(Reqsuest $request)
    {
        return null;
    }

    public function modifyVariantProduct(Request $request, $id)
    {
        return null;
    }

    public function activateDisactivateVariantProduct($id)
    {
        return null;
    }

    public function deleteVariantProduct($id)
    {
        return null;
    }*/


}
