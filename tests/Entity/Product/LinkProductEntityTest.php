<?php


namespace App\Test\Entity\Product;

use App\Entity\Product\Category;
use App\Entity\Product\Product;
use App\Entity\Product\VariantProduct;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class LinkProductEntityTest extends KernelTestCase
{
	protected $variant1 = null;
	protected $variant2 = null;
	protected $product1 = null;
	protected $product2 = null;
	protected $category1 = null;
	protected $category2 = null;

    /**
     *  @return void
     */
	protected function setUp(): void
	{
		parent::setUp();
		self::bootKernel();

		$this->variant1 = new VariantProduct();
		$this->variant1->setName("Variante de produit 1.");
		$this->variant2 = new VariantProduct();
		$this->variant2->setName("Variante de produit 2.");
		$this->product1 = new Product();
		$this->product1->setName("Produit 1.");
		$this->product2 = new Product();
		$this->product2->setName("Produit 2.");
		$this->category1 = new Category();
		$this->category1->setName("Categorie 1.");
		$this->category2 = new Category();
		$this->category2->setName("Categorie 2.");
	}

	//Fonctionnement des tests :
	//ManyToMany : Ajoute de deux objets puis supprime le dernier. À chaque fois on vérifie les contenus dans les deux objets.
	//ManyToOne  : Change deux fois d'objets puis remet la valeur à null. À chaque fois on vérifie la valeur et le contenu des autres objets.
	//OneTOMany  : Ajoute de deux objets puis supprime le dernier, ajoute ensuite un objet déjà contenu dans un autre.
	//             Pour les trois premières modifications on vérifie la valeur et le contenu des autres objets.
	//             Dans le dernier cas on vérifie les contenus des deux autres objets.

	public function testLinkCategoryProduct() 
	{
		$this->assertFalse($this->category1->hasProduct($this->product1), "Erreur la catégorie 1 a déjà le produit 1");
		$this->assertFalse($this->product1->hasCategory($this->category1), "Erreur le produit 1 a déjà la catégorie 1");
		
		$this->category1->addProduct($this->product1);
		$this->assertTrue($this->category1->hasProduct($this->product1), "Erreur addProduct le produit n'a pas été ajouté à la catégorie.");
		$this->assertTrue($this->product1->hasCategory($this->category1), "Erreur addProduct la catégorie n'a pas été ajouté au produit.");
		$this->category1->addProduct($this->product2);
		$this->assertTrue($this->category1->hasProduct($this->product1), "Erreur addProduct l'ancien produit a disparu.");
		$this->assertTrue($this->category1->hasProduct($this->product2), "Erreur addProduct le nouveau produit n'a pas été ajouté.");
		$this->category1->removeProduct($this->product1);
		$this->assertFalse($this->category1->hasProduct($this->product1), "Erreur removeProduct le produit n'a pas été enlevé à la catégorie.");
		$this->assertFalse($this->product1->hasCategory($this->category1), "Erreur removeProduct la catégorie n'a pas été enlevé au produit.");
		$this->category1->removeProduct($this->product2);

		$this->product1->addCategory($this->category1);
		$this->assertTrue($this->category1->hasProduct($this->product1), "Erreur addCategory le produit n'a pas été ajouté à la catégorie.");
		$this->assertTrue($this->product1->hasCategory($this->category1), "Erreur addCategory la catégorie n'a pas été ajouté au produit.");
		$this->product1->addCategory($this->category2);
		$this->assertTrue($this->product1->hasCategory($this->category1), "Erreur addCategory l'ancien produit a disparu.");
		$this->assertTrue($this->product1->hasCategory($this->category2), "Erreur addCategory le nouveau produit n'a pas été ajouté.");
		$this->product1->removeCategory($this->category1);
		$this->assertFalse($this->category1->hasProduct($this->product1), "Erreur removeCategory le produit n'a pas été enlevé à la catégorie.");
		$this->assertFalse($this->product1->hasCategory($this->category1), "Erreur removeCategory la catégorie n'a pas été enlevé au produit.");
	}

	public function testLinkVariantProduct()
	{
		$this->assertNull($this->variant1->getProduct(), "Erreur la variante 1 a déjà un produit.");
		$this->assertFalse($this->product1->hasVariantProduct($this->variant1), "Erreur le produit 1 a déjà la variante 1.");
		
		$this->variant1->setProduct($this->product1);
		$this->assertEquals($this->variant1->getProduct(), $this->product1, "Erreur setProduct le produit de la variant 1 n'a pas été modifié.");
		$this->assertTrue($this->product1->hasVariantProduct($this->variant1), "Erreur setProduct la variante 1 n'a pas été ajouté au produit.");
		$this->variant1->setProduct($this->product2);
		$this->assertEquals($this->variant1->getProduct(), $this->product2, "Erreur setProduct le produit de la variant 1 n'est pas le bon.");
		$this->assertTrue($this->product2->hasVariantProduct($this->variant1), "Erreur setProduct la variante 1 n'a pas été ajouté au produit 2.");
		$this->assertFalse($this->product1->hasVariantProduct($this->variant1), "Erreur setProduct la variante 1 n'a pas été enlevé au produit 1.");
		$this->variant1->setProduct(null);
		$this->assertNull($this->variant1->getProduct());
		$this->assertFalse($this->product2->hasVariantProduct($this->variant1), "Erreur setProduct la variante 1 n'a pas été ajouté au produit 2.");

		$this->product1->addVariantProduct($this->variant1);
		$this->assertTrue($this->product1->hasVariantProduct($this->variant1), "Erreur addVariantProduct la variante 1 n'a pas été ajouté au produit 1.");
		$this->assertEquals($this->variant1->getProduct(), $this->product1, "Erreur addVariantProduct le produit de la variante 1 n'a pas été modifié.");
		$this->product1->addVariantProduct($this->variant2);
		$this->assertTrue($this->product1->hasVariantProduct($this->variant2), "Erreur addVariantProduct la variante 2 n'a pas été ajouté au produit 1.");
		$this->assertTrue($this->product1->hasVariantProduct($this->variant1), "Erreur addVariantProduct la variante 1 a pas été enlevé au produit 1.");
		$this->product1->removeVariantProduct($this->variant2);
		$this->assertFalse($this->product1->hasVariantProduct($this->variant2), "Erreur removeVariantProduct la variante 2 n'a pas été enlevé au produit 1.");
		$this->assertTrue($this->product1->hasVariantProduct($this->variant1), "Erreur removeVariantProduct la variante 1 a été envevé au produit 1.");
		$this->product2->addVariantProduct($this->variant1);
		$this->assertFalse($this->product1->hasVariantProduct($this->variant1), "Erreur addVariantProduct la variante 2 n'a pas été enlevé au produit 1.");
		$this->assertTrue($this->product2->hasVariantProduct($this->variant1), "Erreur addVariantProduct la variante 1 a été envevé au produit 1.");

	}

	public function testLinkVariantCategory()
	{
		$this->assertFalse($this->variant1->hasCategory($this->category1));
		$this->assertFalse($this->category1->hasVariantProduct($this->variant1));

		$this->category1->addVariantProduct($this->variant1);
		$this->assertTrue($this->category1->hasVariantProduct($this->variant1), "Erreur addVariantProduct la variante 1 n'a pas été ajouté à la catégorie.");
		$this->assertTrue($this->variant1->hasCategory($this->category1), "Erreur addVariantProduct la catégorie 1 n'a pas été ajouté à la variante 1.");
		$this->category1->addVariantProduct($this->variant2);
		$this->assertTrue($this->category1->hasVariantProduct($this->variant1), "Erreur addVariantProduct l'ancienne variante a disparu.");
		$this->assertTrue($this->category1->hasVariantProduct($this->variant2), "Erreur addVariantProduct la nouvelle variante n'a pas été ajouté.");
		$this->category1->removeVariantProduct($this->variant1);
		$this->assertFalse($this->category1->hasVariantProduct($this->variant1), "Erreur removeVariantProduct la variante n'a pas été enlevé à la catégorie.");
		$this->assertFalse($this->variant1->hasCategory($this->category1), "Erreur removeVariantProduct la catégorie n'a pas été enlevé de la variante.");
		$this->category1->removeVariantProduct($this->variant2);

		$this->variant1->addCategory($this->category1);
		$this->assertTrue($this->category1->hasVariantProduct($this->variant1), "Erreur addCategory la variante 1 n'a pas été ajouté à la catégorie.");
		$this->assertTrue($this->variant1->hasCategory($this->category1), "Erreur addCategory la catégorie 1 n'a pas été ajouté à la variante 1.");
		$this->variant1->addCategory($this->category2);
		$this->assertTrue($this->variant1->hasCategory($this->category1), "Erreur addCategory l'ancienne category a disparu.");
		$this->assertTrue($this->variant1->hasCategory($this->category2), "Erreur addCategory la nouvelle category n'a pas été ajouté.");
		$this->variant1->removeCategory($this->category2);
		$this->assertFalse($this->variant1->hasCategory($this->category2), "Erreur removeCategory la catégorie n'a pas été enlevé à la variante.");
		$this->assertFalse($this->category2->hasVariantProduct($this->variant1), "Erreur removeCategory la variante n'a pas été enlevé à la catégorie.");
	}

	/**
     *  @return void
     */
	protected function setDown(): void
	{
		parent::tearDown();
	}

}
