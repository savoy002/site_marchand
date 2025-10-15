<?php

namespace App\Test\Entity\Product;

use DateTime;

use App\Entity\Product\Category;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class CategoryEntityTest extends KernelTestCase
{
	protected $category = null;
	protected $memo_caract = null;
	protected $memo_entier = null;

	/**
     *  @return void
     */
	protected function setUp(): void
	{
		parent::setUp();
		self::bootKernel();

		$this->category = new Category();
		$this->memo_caract = "Texte teste";
		$this->memo_entier = 12;
	}

	public function testProduct() {
		$this->assertEquals($this->category->getName(), "", "Erreur dans le nom de base.");
		$this->assertEquals($this->category->getImgFileName(), "", "Erreur dans le chemin de base.");
		$this->assertEquals($this->category->getCode(), "", "Erreur dans le code de base.");
		$this->assertFalse($this->category->getActivate(), "Erreur de activate de base.");
		$this->assertFalse($this->category->getDelete(), "Erreur de Delete de base.");

		$this->category->setName($this->memo_caract);
		$this->category->setImgFileName($this->memo_caract);
		$this->category->setCode($this->memo_caract);
		$this->category->setActivate(true);
		$this->category->setDelete(true);

		$this->assertEquals($this->category->getName(), $this->memo_caract, "Erreur de nom après modification.");
		$this->assertEquals($this->category->getImgFileName(), $this->memo_caract, "Erreur chemin de l'image après modification.");
		$this->assertEquals($this->category->getCode(), $this->memo_caract, "Erreur de code après modification.");
		$this->assertTrue($this->category->getActivate(), "Erreur activate après modification.");
		$this->assertTrue($this->category->getDelete(), "Erreur delete après modification.");
	}

	/**
     *  @return void
     */
	protected function setDown(): void
	{
		parent::tearDown();
	}

}
