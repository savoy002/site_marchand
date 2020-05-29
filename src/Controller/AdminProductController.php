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


class AdminProductController extends AbstractController
{

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
            if($category->getCode() === null || $category->getCode() === "") {
                $code = str_replace(' ', '_', $category->getName());
                $category->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
            }
            $exist = $this->getDoctrine()->getRepository(Category::class)->findBy(['code' => $category->getCode()]);
            if(!empty($exist)) {
                $errors[] = "Le code de la catégorie est déjà utilisé par une autre catégorie.";
                $valid = false;
            }
            if($valid) {
                $image = $form->get('image')->getData();
                if($image) {
                    $res = $this->saveImage($image, 'category_image_directory');
                    if(gettype($res) === "string")
                    	$category->setImgFileName($res);
					else 
                    	return $this->render('store/produts/categories/form_category.html.twig', 
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
            if($category->getCode() === null || $category->getCode() === "") {
                $code = str_replace(' ', '_', $category->getName());
                $category->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
            }
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
        $this->getDoctrine->getManager()->persist($category);
        $this->getDoctrine->getManager()->flush();
        return $this->redirectToRoute('categories');
    }

 	/**
 	 * @Route("/admin/products", name="products")
 	 */
    public function products(Request $request) 
    {
    	$errors = array();
    	$former_request = array();
    	$criteria = ['number_by_page' => self::NUMBER_BY_PAGE];

    	$page = $request->request->get('page');
    	$number_products = intval($this->getDoctrine()->getRepository(Product::class)->countNumberProducts()[0][1]);
    	$number_pages = intval( $number_products / self::NUMBER_BY_PAGE ) + ( ( $number_products % self::NUMBER_BY_PAGE === 0 )?0:1 );

    	if($request->request->get('research') === "research") {
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
            if($request->request->get('orderBy_sortBy') != "none" && $request->request->get('orderBy_sortBy') !== null) {
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
            $products = $this->getDoctrine()->getRepository(Product::class)->adminResearchProduct($criteria);
            $number_products = $this->getDoctrine()->getRepository(Product::class)->adminResearchNumberProducts($criteria)[0][1];
            $number_pages = intval( $number_products / self::NUMBER_BY_PAGE ) + ( ( $number_products % self::NUMBER_BY_PAGE === 0 )?0:1 );
    	} else {
    		$criteria['page'] = 0;
            $page = 1;
    		$products = $this->getDoctrine()->getRepository(Product::class)->adminResearchProduct($criteria);
    	}

        return $this->render('admin/products/products/products.html.twig', 
        	['products' => $products, 'errors' => $errors, 'page' => $page, 'number_pages' => $number_pages, 'request' => $former_request]);
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
       		if($product->getCode() === null || $product->getCode() === '') {
       			$code = str_replace(' ', '_', $product->getName());
                $product->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
       		}
       		$exist = $this->getDoctrine()->getRepository(Product::class)->findBy(['code' => $product->getCode()]);
       		if(!empty($exist))  {
       			$errors[] = "Le code du produit existe déjà.";
       			$valid = false;
       		}
       		if($valid) {
       			$image = $form->get('image')->getData();
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
            if($product->getCode() === null || $product->getCode() === "") {
                $code = str_replace(' ', '_', $product->getName());
                $product->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
            }
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
    	$this->getDoctrine()->getManager()->persist($product);
    	$this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('products');
    }

	/**
 	 * @Route("/admin/variants_products", name="variants_products")
 	 */
    public function variantsProducts(Request $request)
    {
    	$errors = array();
    	$former_request = array();
    	$criteria = ['number_by_page' => self::NUMBER_BY_PAGE];

    	$page = $request->request->get('page');
    	$number_products = intval($this->getDoctrine()->getRepository(VariantProduct::class)->adminCountNumberVariantsProducts()[0][1]);
    	$number_pages = intval( $number_products / self::NUMBER_BY_PAGE ) + ( ( $number_products % self::NUMBER_BY_PAGE === 0 )?0:1 );

    	if($request->request->get('research') === "research") {
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
            if($request->request->get('orderBy_sortBy') != "none" && $request->request->get('orderBy_sortBy') !== null) {
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

            $variants_products = $this->getDoctrine()->getRepository(VariantProduct::class)->adminResearchVariantProduct($criteria);
            $number_products = $this->getDoctrine()->getRepository(VariantProduct::class)->adminResearchNumberVariantsProducts($criteria)[0][1];
            $number_pages = intval( $number_products / self::NUMBER_BY_PAGE ) + ( ( $number_products % self::NUMBER_BY_PAGE === 0 )?0:1 );
    	} else {
    		$page = 1;
    		$criteria['page'] = 0;
    		$variants_products = $this->getDoctrine()->getRepository(VariantProduct::class)->adminResearchVariantProduct($criteria);
    	}

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
        	if($variant_product->getCode() === null || $variant_product->getCode() === '') {
       			$code = str_replace(' ', '_', $variant_product->getName());
                $variant_product->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
       		}
       		$exist = $this->getDoctrine()->getRepository(Product::class)->findBy(['code' => $variant_product->getCode()]);
       		if(!empty($exist))  {
       			$errors[] = "Le code du produit existe déjà.";
       			$valid = false;
       		}
       		if($valid) {
       			$image = $form->get('image')->getData();
                if($image) {
                    $res = $this->saveImage($image, 'variant_product_image_directory');
                    if(gettype($res) === "string")
                    	$variant_product->setImgFileName($res);
					else 
                    	return $this->render('admin/produts/variants_products/form_variant_product.html.twig', 
                            ['form' => $form->createView(), 'errors' => $res, 'create' => true]);
                }
                $variant_product->setStock(0);
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
        $form = $this->createForm(VariantProductType::class, $variant_product);
        $form->handleRequest($request);
        $errors = array();
        if($form->isSubmitted() && $form->isValid()) {
            $valid = true;
            if($variant_product->getCode() === null || $variant_product->getCode() === "") {
                $code = str_replace(' ', '_', $variant_product->getName());
                $variant_product->setCode(transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $code));
            }
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
        $this->getDoctrine()->getManager()->persist($variant_product);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('variants_products');
    }

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

