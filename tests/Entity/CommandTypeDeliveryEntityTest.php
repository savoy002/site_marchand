<?php

namespace App\Tests\Entity;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\Entity\Command\Command;
use App\Entity\Command\TypeDelivery;


class CommandTypeDeliveryEntityTest extends KernelTestCase
{

	protected $command = null;

	protected $typeDelivery1 = null;

	protected $typeDelivery2 = null;

	protected function setUp()
	{
		$kernel = self::bootKernel();

		$this->command = new Command();

		$this->typeDelivery1 = new TypeDelivery();
		$this->typeDelivery1->setName("Type numéro 1.");

		$this->typeDelivery2 = new TypeDelivery();
		$this->typeDelivery2->setName("Type numéro 2.");
	}

	public function testAddAndRemoveCommandToTypeDelivery()
	{
		$this->typeDelivery1->addCommand($this->command);	
		$this->assertContains($this->command, $this->typeDelivery1->getCommands(), "typeDelivery1 ne contient pas command.");
		$this->assertEquals($this->typeDelivery1, $this->command->getTypeDelSelected(), "command ne possède pas typeDelivery1.");
		
		$this->typeDelivery2->addCommand($this->command);
		$this->assertContains($this->command, $this->typeDelivery2->getCommands(), "typeDelivery2 ne contient pas command.");
		$this->assertEquals($this->typeDelivery2, $this->command->getTypeDelSelected(), "command ne possède pas typeDelivery2.");
		$this->assertNotContains($this->command, $this->typeDelivery1->getCommands(), "typeDelivery1 possède toujours command.");

		$this->typeDelivery2->removeCommand($this->command);
		$this->assertNotContains($this->command, $this->typeDelivery2->getCommands(), "typeDelivery1 possède toujours command.");
		$this->assertNotEquals($this->command->getTypeDelSelected(), $this->typeDelivery2, "command possède toujours typeDelivery1.");
		$this->assertNull($this->command->getTypeDelSelected(), "le typeDelivery de command n'a pas été effacée.");
	}

	public function testChangeTypeDeliveryToCommand()
	{
		$this->command->setTypeDelSelected($this->typeDelivery1);
		$this->assertEquals($this->typeDelivery1, $this->command->getTypeDelSelected(), "command ne possède pas typeDelivery1.");
		$this->assertContains($this->command, $this->typeDelivery1->getCommands(), "typeDelivery1 ne contient pas command.");

		$this->command->setTypeDelSelected($this->typeDelivery2);
		$this->assertEquals($this->command->getTypeDelSelected(), $this->typeDelivery2, "command ne possède pas typeDelivery2.");
		$this->assertContains($this->command, $this->typeDelivery2->getCommands(), "typeDelivery2 ne contient pas command.");
		$this->assertNotContains($this->command, $this->typeDelivery1->getCommands(), "typeDelivery1 contient toujours command.");

		$this->command->setTypeDelSelected(null);
		$this->assertNull($this->command->getTypeDelSelected(), "command n'a pas pour typeDeliverye null.");
		$this->assertNotContains($this->command, $this->typeDelivery2->getCommands(), "typeDelivery2 contient toujours command.");
	}

	public function tearDown()
	{
		parent::tearDown();
	}

}

