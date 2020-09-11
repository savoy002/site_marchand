<?php


namespace App\Test\Entity;

use App\Entity\Product\Product;
use App\Entity\Product\VariantProduct;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class ProductVariantProductEntityTest extends KernelTestCase
{

	protected $variant = null;

	protected $product1 = null;

	protected $product2 = null;

	protected function setUp()
	{
		$kernel = self::bootKernel();

		$this->variant = new VariantProduct();
		$this->variant->setName("Variante de produit.");

		$this->product1 = new Product();
		$this->product1->setName("Produit 1.");

		$this->product2 = new Product();
		$this->product2->setName("Produit 2.");

	}

	public function testAddAndRemoveVariantProductToProduct()
	{
		$this->product1->addVariantProduct($this->variant);
		$this->assertContains($this->variant, $this->product1->getVariantsProducts(), "product1 ne contient pas variant.");
		$this->assertEquals($this->variant->getProduct(), $this->product1, "variant ne possède pas product1.");

		$this->product2->addVariantProduct($this->variant);
		$this->assertContains($this->variant, $this->product2->getVariantsProducts(), "product2 ne contient pas variant.");
		$this->assertEquals($this->variant->getProduct(), $this->product2, "variant ne possède pas product2.");
		$this->assertNotContains($this->variant, $this->product1->getVariantsProducts(), 
			"product1 contient toujours vairaint.");

		$this->product2->removeVariantProduct($this->variant);
		$this->assertNotContains($this->variant, $this->product2->getVariantsProducts(), 
			"product2 contient toujours variant.");
		$this->assertNotEquals($this->variant->getProduct(), $this->product2, "variant possède toujours product2.");
		$this->assertNull($this->variant->getProduct(), "le product de variant n'a pas été effacé.");
	}

	public function testChangeProductToVariantProduct()
	{
		$this->variant->setProduct($this->product1);
		$this->assertEquals($this->variant->getProduct(), $this->product1, "variant ne possède pas product1.");
		$this->assertContains($this->variant, $this->product1->getVariantsProducts(), "product1 ne contient pas variant.");

		$this->variant->setProduct($this->product2);
		$this->assertEquals($this->variant->getProduct(), $this->product2, "variant ne possède pas product2.");
		$this->assertContains($this->variant, $this->product2->getVariantsProducts(), "product2 ne contient pas variant.");
		$this->assertNotContains($this->variant, $this->product1->getVariantsProducts(), 
			"product1 contient toujours variant.");

		$this->variant->setProduct(null);
		$this->assertNull($this->variant->getProduct(), "variant n'a pas pour produit null.");
		$this->assertNotContains($this->variant, $this->product2->getVariantsProducts(), 
			"product2 contient toujours variant.");
	}

	protected function setDown()
	{
		parent::tearDown();
	}

}
