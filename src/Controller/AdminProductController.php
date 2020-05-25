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


class AdminProductController extends AbstractController
{

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
        $products = $this->getDoctrine()->getRepository(Product::class)->findBy(['delete' => false]);

        return $this->render('admin/products/products/products.html.twig', ['products' => $products]);
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
                    	return $this->render('store/produts/products/form_product.html.twig', 
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
        $product = $this->getDoctrine()->getRepository(product::class)->find($id);
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
                    			return $this->render('admin/produts/products/form_product.html.twig', 
                        			['form' => $form->createView(), 'errors' => $e[$res_delete], 'create' => false]);
                    	} else {
                    		$product->setImgFileName($res_save);
                    	}
                    } else 
                    	return $this->render('admin/produts/products/form_product.html.twig', 
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
 	 * @Route("/admin/products/variants_produts", name="variants_produts")
 	 */
    public function variantsProducts(Request $request)
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminProductController/variants_products',
        ]);
    }

    /*public function variantProduct()
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

