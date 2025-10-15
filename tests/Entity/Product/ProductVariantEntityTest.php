<?php


namespace App\Test\Entity\Product;

use DateTime;

use App\Entity\Product\VariantProduct;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class ProductVariantEntityTest extends KernelTestCase
{
	protected $variant = null;
	protected $memo_date = null;
	protected $memo_caract = null;
	protected $memo_entier = null;

	/**
     *  @return void
     */
	protected function setUp(): void
	{
		parent::setUp();
		self::bootKernel();

		$this->variant = new VariantProduct();
		$this->memo_date = new DateTime();
		$this->memo_caract = "Texte teste";
		$this->memo_entier = 12;
	}

	public function testProductVariant()
	{
		$this->assertEquals($this->variant->getName(), '', "Erreur nom de base.");
		$this->assertEquals($this->variant->getImgFileName(), '', "Erreur chemin d'image de base.");
		$this->assertEquals($this->variant->getDescription(), '', "Erreur description de base.");
		$this->assertEquals($this->variant->getStock(), 0, "Erreur de stock de base.");
		$this->assertEquals($this->variant->getCode(), '', "Erreur de code de base.");
		$this->assertEquals($this->variant->getPrice(), 0, "Erreur de prix de base.");
		$this->assertFalse($this->variant->getIsWellcome(), "Erreur de IsWellcome de base.");
		$this->assertFalse($this->variant->getActivate(), "Erreur de Activate de base.");
		$this->assertFalse($this->variant->getDelete(), "Erreur de Delete de base.");
		$this->assertNotNull($this->variant->getCreatedAt(), "Erreur de date de création de base, elle ne doit pas être null.");
		$this->assertNotEquals($this->variant->getCreatedAt(), $this->memo_date, 
			"Erreur de date de création de base, elle doit être différente de la date de mémoire");

		$this->variant->setName($this->memo_caract);
		$this->variant->setImgFileName($this->memo_caract);
		$this->variant->setDescription($this->memo_caract);
		$this->variant->setStock($this->memo_entier);
		$this->variant->setCode($this->memo_caract);
		$this->variant->setPrice($this->memo_entier);
		$this->variant->setIsWellcome(true);
		$this->variant->setActivate(true);
		$this->variant->setDelete(true);
		$this->variant->setCreatedAt($this->memo_date);

		$this->assertEquals($this->variant->getName(), $this->memo_caract, "Erreur de nom après modification.");
		$this->assertEquals($this->variant->getImgFileName(), $this->memo_caract, "Erreur chemin de l'image après modification.");
		$this->assertEquals($this->variant->getDescription(), $this->memo_caract, "Erreur de description après modification.");
		$this->assertEquals($this->variant->getStock(), $this->memo_entier, "Erreur de stock après modification.");
		$this->assertEquals($this->variant->getCode(), $this->memo_caract, "Erreur de code après modification.");
		$this->assertEquals($this->variant->getPrice(), $this->memo_entier, "Erreur de prix après modification.");
		$this->assertTrue($this->variant->getIsWellcome(), "Erreur isWellcome après modification.");
		$this->assertTrue($this->variant->getActivate(), "Erreur activate après modification.");
		$this->assertTrue($this->variant->getDelete(), "Erreur delete après modification.");
		$this->assertEquals($this->variant->getCreatedAt(), $this->memo_date, "Erreur de date de création après modification.");
	}

	/**
     *  @return void
     */
	protected function setDown(): void
	{
		parent::tearDown();
	}

}