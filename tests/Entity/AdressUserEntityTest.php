<?php


namespace App\Tests\Entity;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\Entity\Command\Adress;
use App\Entity\User\User;


class AdressUserEntityTest extends KernelTestCase 
{

	protected $user = null;

	protected $adress1 = null;

	protected $adress2 = null;

	protected function setUp() 
	{
		$kernel = self::bootKernel();

		$this->user = new User();
		$this->user->setUsername('Utilisateur test');

		$this->adress1 = new Adress();
		$this->adress1->setStreet("Rue test 1");
		$this->adress1->setZipCode('00001');
		$this->adress1->setCity("Ville 1");

		$this->adress2 = new Adress();
		$this->adress2->setStreet("Rue test 2");
		$this->adress2->setZipCode('00002');
		$this->adress2->setCity("Ville 2");
	}

	public function testAddAndRemoveUserToAdress()
	{
		$this->adress1->addBelong($this->user);
		$this->assertContains($this->user, $this->adress1->getBelongs(), "adress1 ne contient pas user.");
		$this->assertEquals($this->adress1, $this->user->getLive(), "user ne possède pas adress1.");

		$this->adress2->addBelong($this->user);
		$this->assertContains($this->user, $this->adress2->getBelongs(), "adress2 ne contient pas user.");
		$this->assertEquals($this->adress2, $this->user->getLive(), "user ne possède pas adress2.");
		$this->assertNotContains($this->user, $this->adress1->getBelongs(), "adress1 possède toujours user.");

		$this->adress2->removeBelong($this->user);
		$this->assertNotContains($this->user, $this->adress2->getBelongs(), "adress1 possède toujours user.");
		$this->assertNotEquals($this->user->getLive(), $this->adress2, "user possède toujours adress1.");
		$this->assertNull($this->user->getLive(), "l'adress de user n'a pas été effacée.");
	}

	public function testChangeAdressToUser() 
	{
		$this->user->setLive($this->adress1);
		$this->assertEquals($this->adress1, $this->user->getLive(), "user ne possède pas adress1.");
		$this->assertContains($this->user, $this->adress1->getBelongs(), "adress1 ne contient pas user.");

		$this->user->setLive($this->adress2);
		$this->assertEquals($this->user->getLive(), $this->adress2, "user ne possède pas adress2.");
		$this->assertContains($this->user, $this->adress2->getBelongs(), "adress2 ne contient pas user.");
		$this->assertNotContains($this->user, $this->adress1->getBelongs(), "adress1 contient toujours user.");

		$this->user->setLive(null);
		$this->assertNull($this->user->getLive(), "user n'a pas pour adresse null.");
		$this->assertNotContains($this->user, $this->adress2->getBelongs(), "adress2 contient toujours user.");
	}

	protected function tearDown()
	{
		parent::tearDown();
	}

}
