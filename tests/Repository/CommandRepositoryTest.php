<?php


namespace App\Tests\Repository;

use App\Entity\Command\Command;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use DateTime;


class CommandRepositoryTest extends KernelTestCase {


	/**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    const NUMBER_BY_PAGE = 5;


    protected function setUp() 
    {

       $kernel = self::bootKernel();
       $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

    }

    /**
     * Test sans critère de sélection.
     */
    public function testAdminResearchCommandAndNumberCommands()
    {
        $commandsFindBy = $this->entityManager->getRepository(Command::class)->findBy(['delete' => false]);
        $criteria = array("number_by_page" => self::NUMBER_BY_PAGE);
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $numberCommands = $this->entityManager->getRepository(Command::class)->adminResearchNumberCommands($criteria)[0][1];
        $this->assertEquals(sizeof($commandsFindBy), $numberCommands, 
            "La méthode adminResearchNumberCommands ne retourne pas le nombre de Commands recherché.");
        if(sizeof($commands) >= self::NUMBER_BY_PAGE)
            $this->assertEquals(self::NUMBER_BY_PAGE, sizeof($commands), 
                "Le nombre de Commands retourné par adminResearchCommands n'est pas égale au nombre de Commands par page.");
        else
            $this->assertEquals(sizeof($commandsFindBy), sizeof($commands), 
                "Le nombre de Commands retourné par adminResearchCommands n'est pas égale au nombre de Commands recherché");
    }

    /**
     * Vérification du nombre de commandes renvoyé correspond à la page demandée sans recherche.
     * Les tests ne fonctionnent pas si le nombre de Commands dans la base de données est inférieur à 5.
     */
    public function testAdminResearchCommandWithPage()
    {
        $criteria = array("number_by_page" => self::NUMBER_BY_PAGE);
        $number_page = 1;
        $criteria['page'] = strval($number_page);
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        while(sizeof($commands) === self::NUMBER_BY_PAGE) {
            $number_page++;
            $criteria['page'] = strval($number_page);
            $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        }
        $numberTotalCommands = $this->entityManager->getRepository(Command::class)->countNumberCommands()[0][1];
        $this->assertEquals($numberTotalCommands - (self::NUMBER_BY_PAGE * $number_page), sizeof($commands), 
            'Le nombre de page est de '.$number_page);
    }

    /**
     * Vérification des recherches de commandes crées avant ou après des dates définies.
     */
    public function testAdminResearchCommandWithCreatedDate() 
    {
        $criteria = array("number_by_page" => self::NUMBER_BY_PAGE);
        $criteria['createdBefore'] = '2020-03-07';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $date_before = new DateTime('2020-03-07');
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach ($commands as $command) {
            $this->assertTrue($command->getCreatedAt() <= $date_before, 
                'Une commande a une date de création '. $command->getCreatedAt()->format('d/m/Y') .
                ' qui est plus récente que la date recherchée '. $date_before->format('d/m/Y'));
        }
        unset($criteria['createdBefore']);
        $criteria['createdAfter'] = '2020-01-24';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $date_after = new DateTime('2020-01-24');
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach($commands as $command) {
            $this->assertTrue($command->getCreatedAt() >= $date_after, 
                'Une commande a une date de création '. $command->getCreatedAt()->format('d/m/Y') .
                ' qui est moins récente que la date recherchée '. $date_after->format('d/m/Y') );
        }
        $criteria['createdBefore'] = '2020-03-07';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach($commands as $command) {
            $this->assertTrue($command->getCreatedAt() <= $date_before, 
                'Une commande a une date de création '. $command->getCreatedAt()->format('d/m/Y') .
                ' qui est plus récente que la date recherchée '. $date_before->format('d/m/Y') );
            $this->assertTrue($command->getCreatedAt() >= $date_after, 
                'Une commande a une date de création '. $command->getCreatedAt()->format('d/m/Y') .
                ' qui est moins récente que la date recherchée '. $date_after->format('d/m/Y') );
        }
    }

    /**
     * Vérification des recherches de commandes envoyées avant ou après des dates définies.
     */
    public function testAdminResarchCommandWithSentDate()
    {
        $criteria = array("number_by_page" => self::NUMBER_BY_PAGE);
        $criteria['sentBefore'] = '2020-06-30';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $date_before = new DateTime('2020-06-30');
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach ($commands as $command) {
            $this->assertNotNull($command->getDelivery(), "Une commande retournée n'a pas de livraison.");
            $this->assertTrue($command->getDelivery()->getDate() < $date_before, 
                "Une commande a une date de d'envoie ". $command->getDelivery()->getDate()->format('d/m/Y') .
                " qui est plus récente à la date recherchée ". $date_before->format('d/m/Y'));
        }
        unset($criteria['sentBefore']);
        $criteria['sentAfter'] = '2020-05-29';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $date_after = new DateTime('2020-05-29');
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach($commands as $command) {
            $this->assertNotNull($command->getDelivery(), "Une commande retournée n'a pas de livraison.");
            $this->assertTrue($command->getDelivery()->getDate() > $date_after, 
                "Une commande a une date d'envoie ". $command->getDelivery()->getDate()->format('d/m/Y') .
                " qui est moins récente à la date recherchée ". $date_after->format('d/m/Y'));
        }
        $criteria['sentBefore'] = '2020-06-30';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach($commands as $command) {
            $this->assertNotNull($command->getDelivery(), "Une commande retournée n'a pas de livraison.");
            $this->assertTrue($command->getDelivery()->getDate() < $date_before, 
                "Une commande a une date d'envoie ". $command->getDelivery()->getDate()->format('d/m/Y') .
                " qui est plus récente à la date recherchée ". $date_before->format('d/m/Y'));
            $this->assertTrue($command->getDelivery()->getDate() > $date_after, 
                "Une commande a une date d'envoie ". $command->getDelivery()->getDate()->format('d/m/Y')  .
                " qui est moins récente à la date recherchée ". $date_after->format('d/m/Y'));
        }
    }

    /**
     * Vérification des recherches de commandes reçues avant ou après des dates définies.
     */
    public function testAdminResarchCommandWithReceivedDate()
    {
        $criteria = array("number_by_page" => self::NUMBER_BY_PAGE);
        $criteria['receivedBefore'] = '2020-07-15';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $date_before = new DateTime('2020-07-15');
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach ($commands as $command) {
            $this->assertTrue($command->getDateReceive() <= $date_before, 
                'Une commande a une date de réception '. $command->getDateReceive()->format('d/m/Y') .
                ' qui est plus récente à la date recherchée '.$date_before->format('d/m/Y'));
        }
        unset($criteria['receivedBefore']);
        $criteria['receivedAfter'] = '2020-05-21';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $date_after = new DateTime('2020-05-21');
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach($commands as $command) {
            $this->assertTrue($command->getDateReceive() >= $date_after, 
                'Une commande a une date de réception '. $command->getDateReceive()->format('d/m/Y') .
                ' qui est moins récente à la date recherchée '.$date_after->format('d/m/Y'));
        }
        $criteria['receivedBefore'] = '2020-07-15';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach($commands as $command) {
            $this->assertTrue($command->getDateReceive() <= $date_before, 
                'Une commande a une date de réception '. $command->getDateReceive()->format('d/m/Y') .
                ' qui est plus récente à la date recherchée '.$date_before->format('d/m/Y'));
            $this->assertTrue($command->getDateReceive() >= $date_after, 
                'Une commande a une date de réception '. $command->getDateReceive()->format('d/m/Y') .
                ' qui est moins récente à la date recherchée '.$date_after->format('d/m/Y'));
        }
    }

    /**
     * Vérification des recherches de commandes en fonction d'un prix donné.
     */
    public function testAdminResearchCommandWithPrice()
    {
        $criteria = array("number_by_page" => self::NUMBER_BY_PAGE);
        $criteria['price'] = ['type' => 'equal', 'value' => '2700'];
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach ($commands as $command) {
            $this->assertTrue($command->getPriceTotal() >= 2700 && $command->getPriceTotal() <= 2800, 
                "Le prix d'une commande est de ".$command->getPriceTotal()." alors qu'elle devrait être entre 2700 et 2800.");
        }
        $criteria['price'] = ['type' => 'inferior', 'value' => '5200'];
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach ($commands as $command) {
            $this->assertTrue($command->getPriceTotal() <= 5200, 
                "Le prix d'une commande est de ".$command->getPriceTotal()." alors qu'elle devrait être inférieur à 5200.");
        }
        $criteria['price'] = ['type' => 'higher', 'value' => '4800'];
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach ($commands as $command) {
            $this->assertTrue($command->getPriceTotal() >= 4800, 
                "Le prix d'une commande est de ".$command->getPriceTotal()." alors qu'elle devrait être supérieur à 4800.");
        }
    }

    ///**
    // * Vérification des recherches de commandes en fonction de l'adresse de la livraison.
    // */
    /*public function testAdminResearchCommandWithAddress()
    {
        $criteria = array("number_by_page" => self::NUMBER_BY_PAGE);
        $criteria['address'] = ['type' => 'completed', 'value' => '1'];
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach ($commands as $command) {
            $this->assertContains('1', 
                $command->getPlaceDel()->getStreet().' '.$command->getPlaceDel()->getZipCode().' '.$command->getPlaceDel()->getCity(), 
                "L'adresse ne contient pas le mot de demandé.");
        }
        $criteria['address'] = ['type' => 'street', 'value' => 'cran'];
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach ($commands as $command) {
            $this->assertNotFalse(strpos($command->getPlaceDel()->getStreet(), 'cran'), 
                "L'adresse ne contient pas le mot de demandé dans la rue.");
        }
        $criteria['address'] = ['type' => 'zip_code', 'value' => '67'];
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach ($commands as $command) {
            $this->assertNotFalse(strpos($command->getPlaceDel()->getZipCode(), '67'), 
                "L'adresse ne contient pas le mot de demandé dans code postale.");
        }
        $criteria['address'] = ['type' => 'city', 'value' => 'STRAS'];
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach ($commands as $command) {
            $this->assertNotFalse(strpos($command->getPlaceDel()->getCity(), 'Stras'), 
                "L'adresse ne contient pas le mot de demandé dans la ville.");
        }
    }*/

    /**
     * Vérification des recherches de commandes en fonction de l'état de la commande.
     */
    public function testAdminResearchCommandWithStatus()
    {
        $criteria = array("number_by_page" => self::NUMBER_BY_PAGE);
        $criteria['status'] = 'completed';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach($commands as $command) {
            $this->assertTrue($command->getCompleted(), "Une commande n'est pas complète.");
        }
        $criteria['status'] = 'notCompleted';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach($commands as $command) {
            $this->assertFalse($command->getCompleted(), "Une commande est complète.");
        }
        $criteria['status'] = 'notReceived';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach($commands as $command) {
            $this->assertNull($command->getDateReceive(), "Une commande possède une date de réception.");
        }
        $criteria['status'] = 'notSend';
        $commands = $this->entityManager->getRepository(Command::class)->adminResearchCommands($criteria);
        $this->assertFalse(empty($commands), 'Aucune commande retournée.');
        foreach($commands as $command) {
            if($command->getDelivery() === null)
                $this->assertNull($command->getDelivery());
            else
                $this->assertNull($command->getDelivery()->getDate());
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
