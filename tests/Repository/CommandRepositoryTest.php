<?php


namespace App\Tests\Repository;

use App\Entity\Command\Command;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;



class CommandRepositoryTest extends KernelTestCase {


	/**
     * @var \Doctrine\ORM\EntityManager
     */
    private EntityManager;


    protected function setUp() 
    {

       $kernel = self::bootKernel();
       $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

    }

    public function testAdminResearchNumberCommands()
    {
    	$criteria = array();
    	$numberCommands = $this->entityManager->getRepository(Command::class)->adminResearchNumberCommands($criteria)[0][1];
    	$this->AssertEquals(8, $numberCommands);

    	//Ajouter ou enlever des critère au tableau $criteria pour vérifier la function dans d'autres cas.



    }

    public function testAdminResearchCommand()
    {

    	$criteria = array();
    	$commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
    	
    }


    protected function tearDown()
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

}
