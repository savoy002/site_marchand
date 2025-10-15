<?php

namespace App\Test\Entity\Product;

use DateTime;

use App\Entity\Product\Product;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class ProductEntityTest extends KernelTestCase
{
	protected $product = null;
	protected $memo_caract = null;
	protected $memo_entier = null;

	/**
     *  @return void
     */
	protected function setUp(): void
	{
		parent::setUp();
		self::bootKernel();

		$this->product = new Product();
		$this->memo_caract = "Texte teste";
		$this->memo_entier = 12;
	}

	public function testProduct() {
		$this->assertEquals($this->product->getName(), "", "Erreur dans le nom de base");
		$this->assertEquals($this->product->getImgFileName(), "", "Erreur dans le chemin de base");
		$this->assertEquals($this->product->getDescription(), "", "Erreur dans la description de base");
		$this->assertEquals($this->product->getStock(), 0, "Erreur dans le stock de base");
		$this->assertEquals($this->product->getCode(), "", "Erreur dans le code de base");
		$this->assertFalse($this->product->getActivate(), "Erreur de activate de base");
		$this->assertFalse($this->product->getDelete(), "Erreur de Delete de base");

		$this->product->setName($this->memo_caract);
		$this->product->setImgFileName($this->memo_caract);
		$this->product->setDescription($this->memo_caract);
		$this->product->setStock($this->memo_entier);
		$this->product->setCode($this->memo_caract);
		$this->product->setActivate(true);
		$this->product->setDelete(true);

		$this->assertEquals($this->product->getName(), $this->memo_caract, "Erreur de nom après modification.");
		$this->assertEquals($this->product->getImgFileName(), $this->memo_caract, "Erreur chemin de l'image après modification.");
		$this->assertEquals($this->product->getDescription(), $this->memo_caract, "Erreur de description après modification.");
		$this->assertEquals($this->product->getStock(), $this->memo_entier, "Erreur de stock après modification.");
		$this->assertEquals($this->product->getCode(), $this->memo_caract, "Erreur de code après modification.");
		$this->assertTrue($this->product->getActivate(), "Erreur activate après modification.");
		$this->assertTrue($this->product->getDelete(), "Erreur delete après modification.");
	}

	/**
     *  @return void
     */
	protected function setDown(): void
	{
		parent::tearDown();
	}

}
