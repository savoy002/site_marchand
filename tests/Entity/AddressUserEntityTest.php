<?php


namespace App\Tests\Entity;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\Entity\Command\Address;
use App\Entity\User\User;


class AddressUserEntityTest extends KernelTestCase 
{

	protected $user = null;

	protected $address1 = null;

	protected $address2 = null;

	protected function setUp() 
	{
		$kernel = self::bootKernel();

		$this->user = new User();
		$this->user->setUsername('Utilisateur test');

		$this->address1 = new Address();
		$this->address1->setStreet("Rue test 1");
		$this->address1->setZipCode('00001');
		$this->address1->setCity("Ville 1");

		$this->address2 = new Address();
		$this->address2->setStreet("Rue test 2");
		$this->address2->setZipCode('00002');
		$this->address2->setCity("Ville 2");
	}

	public function testAddAndRemoveUserToAddress()
	{
		$this->address1->addBelong($this->user);
		$this->assertContains($this->user, $this->address1->getBelongs(), "address1 ne contient pas user.");
		$this->assertEquals($this->address1, $this->user->getLive(), "user ne possède pas address1.");

		$this->address2->addBelong($this->user);
		$this->assertContains($this->user, $this->address2->getBelongs(), "address2 ne contient pas user.");
		$this->assertEquals($this->address2, $this->user->getLive(), "user ne possède pas address2.");
		$this->assertNotContains($this->user, $this->address1->getBelongs(), "address1 possède toujours user.");

		$this->address2->removeBelong($this->user);
		$this->assertNotContains($this->user, $this->address2->getBelongs(), "address1 possède toujours user.");
		$this->assertNotEquals($this->user->getLive(), $this->address2, "user possède toujours address1.");
		$this->assertNull($this->user->getLive(), "l'address de user n'a pas été effacée.");
	}

	public function testChangeAddressToUser() 
	{
		$this->user->setLive($this->address1);
		$this->assertEquals($this->address1, $this->user->getLive(), "user ne possède pas address1.");
		$this->assertContains($this->user, $this->address1->getBelongs(), "address1 ne contient pas user.");

		$this->user->setLive($this->address2);
		$this->assertEquals($this->user->getLive(), $this->address2, "user ne possède pas address2.");
		$this->assertContains($this->user, $this->address2->getBelongs(), "address2 ne contient pas user.");
		$this->assertNotContains($this->user, $this->address1->getBelongs(), "address1 contient toujours user.");

		$this->user->setLive(null);
		$this->assertNull($this->user->getLive(), "user n'a pas pour addresse null.");
		$this->assertNotContains($this->user, $this->address2->getBelongs(), "address2 contient toujours user.");
	}

	protected function tearDown()
	{
		parent::tearDown();
	}

}
