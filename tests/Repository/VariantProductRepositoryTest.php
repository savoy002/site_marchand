<?php


namespace App\Tests\Repository;

use App\Entity\Product\VariantProduct;
use App\Entity\Product\Product;
use App\Entity\Product\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VariantProductRepositoryTest extends KernelTestCase {

	/**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    const NUMBER_BY_PAGE = 6;


    protected function setUp() 
    {

       $kernel = self::bootKernel();
       $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

    }

    /**
     * Test avec des Categories.
     */
    public function testStoreResearchVariantProductAndNumberByCategories()
    {
    	$criteria = array('number_by_page' => self::NUMBER_BY_PAGE);
    	//Vérification des recherches avec une seul Category.
    	$category1 = $this->entityManager->getRepository(Category::class)->findOneBy(['code' => 'fruit']);
    	$criteria['categories'] = ['0' => $category1];
    	$var_products = $this->entityManager->getRepository(VariantProduct::class)->storeResearchVariantProduct($criteria);
    	$number_var_products = 
    		$this->entityManager->getRepository(VariantProduct::class)->storeResearchNumberVariantProduct($criteria)[0][1];
    	$this->assertEquals($number_var_products, sizeof($category1->getVariantsProducts()->getValues()),
    		 "Le nombre de VariantProduct retournés n'est pas le nombre de VariantProduct contenu dans le Product poire.");
    	foreach($var_products as $var_product) {
    		$this->assertTrue($category1->hasVariantProduct($var_product), 
    			"La VariantProduct ".$var_product->getName()." n'est pas contenu dans le Product ".$category1->getName());
    	}
    	foreach ($category1->getVariantsProducts() as $category1_var_product) {
    		$this->assertContains($category1_var_product, $var_products, 
    			"La VariantProduct ".$category1_var_product->getName()." du Product ".$category1->getName().
    			" n'est pas contenu dans le résultat de requête.");
    	}
    	//Vérification des recherches avec plusieurs Categories.
    	$category2 = $this->entityManager->getRepository(Category::class)->findOneBy(['code' => 'soupe']);
    	$criteria["categories"]['1'] = $category2;
    	$var_products = $this->entityManager->getRepository(VariantProduct::class)->storeResearchVariantProduct($criteria);
    	$number_var_products = 
    		$this->entityManager->getRepository(VariantProduct::class)->storeResearchNumberVariantProduct($criteria)[0][1];
    	$this->assertEquals($number_var_products, 
    		sizeof($category1->getVariantsProducts()->getValues()) + sizeof($category2->getVariantsProducts()->getValues()),
    		"Le nombre de VariantProduct retournés n'est pas le nombre de VariantProduct contenu dans les Product poire et poireau.");
    	foreach($var_products as $var_product) {
    		$this->assertTrue($category1->hasVariantProduct($var_product) || $category2->hasVariantProduct($var_product), 
    			"La VariantProduct ".$var_product->getName()." n'est pas contenu dans le Product "
    			.$category1->getName()." ou ".$category2->getName());
    	}
    	foreach ($category1->getVariantsProducts() as $category1_var_product) {
    		$this->assertContains($category1_var_product, $var_products, 
    			"La VariantProduct ".$category1_var_product->getName()." du Product ".$category1->getName().
    			" n'est pas contenu dans le résultat de requête.");
    	}
    	foreach ($category2->getVariantsProducts() as $product2_var_product) {
    		$this->assertContains($product2_var_product, $var_products, 
    			"La VariantProduct ".$product2_var_product->getName()." du Product ".$category2->getName().
    			" n'est pas contenu dans le résultat de requête.");
    	}
    	//Vérification des recherches avec doublons.
    	$category1 = $this->entityManager->getRepository(Category::class)->findOneBy(['code' => 'legume']);
    	$criteria["categories"]['0'] = $category1;
    	$var_products = $this->entityManager->getRepository(VariantProduct::class)->storeResearchVariantProduct($criteria);
    	$number_var_products = 
    		$this->entityManager->getRepository(VariantProduct::class)->storeResearchNumberVariantProduct($criteria)[0][1];
    	$number_var_products_ask = sizeof($category1->getVariantsProducts());
    	foreach($category2->getVariantsProducts() as $var_product) {
    		if(!$category1->hasVariantProduct($var_product))
    			$number_var_products_ask++;
    	}
    	$this->assertEquals($number_var_products, $number_var_products_ask,
    		"Le nombre de VariantProduct retournés n'est pas le nombre de VariantProduct contenu dans les Category ".
    		$category1->getName()." et ".$category2->getName().".");
    	foreach($var_products as $var_product) {
    		$this->assertTrue($category1->hasVariantProduct($var_product) || $category2->hasVariantProduct($var_product), 
    			"La VariantProduct ".$var_product->getName()." n'est pas contenu dans le Product "
    			.$category1->getName()." ou ".$category2->getName());
    	}
    	foreach ($category1->getVariantsProducts() as $category1_var_product) {
    		$this->assertContains($category1_var_product, $var_products, 
    			"La VariantProduct ".$category1_var_product->getName()." du Product ".$category1->getName().
    			" n'est pas contenu dans le résultat de requête.");
    	}
    	foreach ($category2->getVariantsProducts() as $product2_var_product) {
    		$this->assertContains($product2_var_product, $var_products, 
    			"La VariantProduct ".$product2_var_product->getName()." du Product ".$category2->getName().
    			" n'est pas contenu dans le résultat de requête.");
    	}
    	$memory = array();
    	foreach ($var_products as $var_product) {
    		$this->assertNotContains($var_product, $memory, 
    			"La VariantProduct ".$var_product->getName()." est contenu deux fois dans le rsultat de recherche.");
    		$memory[] = $var_product;
    	}
    }

    /**
     * Test avec des Products.
     */
    public function testStoreResearchVariantProductAndNumberByProducts()
    {
    	$criteria = array('number_by_page' => self::NUMBER_BY_PAGE);
    	//Recherche avec un seul Product.
    	$product1 = $this->entityManager->getRepository(Product::class)->findOneBy(['code' => 'asperge']);
    	$criteria['products'] = ['0' => $product1];
    	$var_products = $this->entityManager->getRepository(VariantProduct::class)->storeResearchVariantProduct($criteria);
    	$number_var_products = 
    		$this->entityManager->getRepository(VariantProduct::class)->storeResearchNumberVariantProduct($criteria)[0][1];
    	$this->assertEquals($number_var_products, sizeof($product1->getVariantsProducts()->getValues()),
    		 "Le nombre de VariantProduct retournés n'est pas le nombre de VariantProduct contenu dans le Product "
    		 .$product1->getName().".");
    	foreach($var_products as $var_product) {
    		$this->assertTrue($product1->hasVariantProduct($var_product), 
    			"La VariantProduct ".$var_product->getName()." n'est pas contenu dans le Product ".$product1->getName());
    	}
    	foreach ($product1->getVariantsProducts() as $product1_var_product) {
    		$this->assertContains($product1_var_product, $var_products, 
    			"La VariantProduct ".$product1_var_product->getName()." du Product ".$product1->getName().
    			" n'est pas contenu dans le résultat de requête.");
    	}
    	//Recherche avec plusieurs Products.
    	$product2 = $this->entityManager->getRepository(Product::class)->findOneBy(['code' => 'poireau']);
    	$criteria["products"]['1'] = $product2;
    	$var_products = $this->entityManager->getRepository(VariantProduct::class)->storeResearchVariantProduct($criteria);
    	$number_var_products = 
    		$this->entityManager->getRepository(VariantProduct::class)->storeResearchNumberVariantProduct($criteria)[0][1];
    	$this->assertEquals($number_var_products, 
    		sizeof($product1->getVariantsProducts()->getValues()) + sizeof($product2->getVariantsProducts()->getValues()),
    		"Le nombre de VariantProduct retourné n'est pas le nombre de VariantProduct contenu dans les Product "
    		.$product1->getName()." et ".$product2->getName().".");
    	foreach($var_products as $var_product) {
    		$this->assertTrue($product1->hasVariantProduct($var_product) || $product2->hasVariantProduct($var_product), 
    			"La VariantProduct ".$var_product->getName()." n'est pas contenu dans le Product "
    			.$product1->getName()." ou ".$product2->getName());
    	}
    	foreach ($product1->getVariantsProducts() as $product1_var_product) {
    		$this->assertContains($product1_var_product, $var_products, 
    			"La VariantProduct ".$product1_var_product->getName()." du Product ".$product1->getName().
    			" n'est pas contenu dans le résultat de requête.");
    	}
    	foreach ($product2->getVariantsProducts() as $product2_var_product) {
    		$this->assertContains($product2_var_product, $var_products, 
    			"La VariantProduct ".$product2_var_product->getName()." du Product ".$product2->getName().
    			" n'est pas contenu dans le résultat de requête.");
    	}
    }

    public function testStoreResearchVariantProductAndNumberByCategoryAndProducts()
    {
    	$criteria = array('number_by_page' => self::NUMBER_BY_PAGE);
    	$category = $this->entityManager->getRepository(Category::class)->findOneBy(['code' => 'fruit']);
    	$product = $this->entityManager->getRepository(Product::class)->findOneBy(['code' => 'asperge']);
    	$criteria['categories'] = array('0' => $category);
    	$criteria['products'] = array('0' => $product);
    	$var_products = $this->entityManager->getRepository(VariantProduct::class)->storeResearchVariantProduct($criteria);
    	$number_var_products = 
    		$this->entityManager->getRepository(VariantProduct::class)->storeResearchNumberVariantProduct($criteria)[0][1];
    	$this->assertEquals($number_var_products, 
    		sizeof($product->getVariantsProducts()->getValues()) + sizeof($category->getVariantsProducts()->getValues()), 
    		"Le nombre de VariantProduct retourné n'est pas le nombre de VariantProduct contenu dans le Product "
    		.$product->getName()." et la Category ".$category->getName().".");
    	foreach($var_products as $var_product) {
    		$this->assertTrue($product->hasVariantProduct($var_product) || $category->hasVariantProduct($var_product), 
    			"La VariantProduct ".$var_product->getName()." n'est pas contenu dans le Product "
    			.$product->getName()." ou la Category ".$category->getName().".");
    	}
    	foreach ($product->getVariantsProducts() as $product_var_product) {
    		$this->assertContains($product_var_product, $var_products, 
    			"La VariantProduct ".$product_var_product->getName()." du Product ".$product->getName().
    			" n'est pas contenu dans le résultat de requête.");
    	}
    	foreach ($category->getVariantsProducts() as $category_var_product) {
    		$this->assertContains($category_var_product, $var_products, 
    			"La VariantProduct ".$category_var_product->getName()." de la Category ".$category->getName().
    			" n'est pas contenu dans le résultat de requête.");
    	}
    }

    public function testStoreResearchVariantProductWithPage() 
    {
    	$criteria = array('number_by_page' => self::NUMBER_BY_PAGE);

    	//Pour une page.

    	//Choix des Category.
    	$category1 = $this->entityManager->getRepository(Category::class)->findOneBy(['code' => 'fruit']);
    	$category2 = $this->entityManager->getRepository(Category::class)->findOneBy(['code' => 'boite_concerve']);
    	$criteria['categories'] = array('0' => $category1, '1' => $category2);
    	//Calcule le nombre de VariantProduct de la requête.
    	$number_var_products = 
    		$this->entityManager->getRepository(VariantProduct::class)->storeResearchNumberVariantProduct($criteria)[0][1];
    	//Calcule le nombre de pages.
    	$number_pages = intval( $number_var_products / self::NUMBER_BY_PAGE ) + 
            ( ( $number_var_products % self::NUMBER_BY_PAGE === 0 )?0:1 );
        for($page = 0; $page < $number_pages; $page++) {
        	$criteria['page'] = $page;
        	$var_products = $this->entityManager->getRepository(VariantProduct::class)->storeResearchVariantProduct($criteria);
        	if($page != $number_pages - 1) {
        		$this->assertEquals(sizeof($var_products), self::NUMBER_BY_PAGE, 
        			"Le nombre de VariantProduct retourné à la page ".$page." est ".sizeof($var_products).
        			" mais devrait être ".self::NUMBER_BY_PAGE.".");
        	} else {
        		$this->assertEquals(sizeof($var_products), ($number_var_products % 6  == 0)?6:($number_var_products % 6) , 
        			"Le nombre de VariantProduct retourné à la dernière page est ".sizeof($var_products).
        			" mais devrait être ".($number_var_products % 6  == 0)?6:($number_var_products % 6).".");
        	}
        }

        //Pour plusieurs pages.

        //Choix des Category.
        $category3 = $this->entityManager->getRepository(Category::class)->findOneBy(['code' => 'legume']);
        $criteria['categories']['2'] = $category3;
		//Calcule le nombre de VariantProduct de la requête.
    	$number_var_products = 
    		$this->entityManager->getRepository(VariantProduct::class)->storeResearchNumberVariantProduct($criteria)[0][1];
    	//Calcule le nombre de pages.
    	$number_pages = intval( $number_var_products / self::NUMBER_BY_PAGE ) + 
            ( ( $number_var_products % self::NUMBER_BY_PAGE === 0 )?0:1 );
        for($page = 0; $page < $number_pages; $page++) {
        	$criteria['page'] = $page;
        	$var_products = $this->entityManager->getRepository(VariantProduct::class)->storeResearchVariantProduct($criteria);
        	if($page != $number_pages - 1) {
        		$this->assertEquals(sizeof($var_products), self::NUMBER_BY_PAGE, 
        			"Le nombre de VariantProduct retourné à la page ".$page." est ".sizeof($var_products).
        			" mais devrait être ".self::NUMBER_BY_PAGE.".");
        	} else {
        		$this->assertEquals(sizeof($var_products), ($number_var_products % 6  == 0)?6:($number_var_products % 6) , 
        			"Le nombre de VariantProduct retourné à la dernière page est ".sizeof($var_products).
        			" mais devrait être ".($number_var_products % 6  == 0)?6:($number_var_products % 6).".");
        	}
        }

    }

    protected function tearDown()
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

}

