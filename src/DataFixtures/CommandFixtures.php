<?php

namespace App\DataFixtures;

use DateTime;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use App\DataFixtures\ProductFixtures;
use App\DataFixtures\UserFixtures;

use App\Entity\Command\Command;
use App\Entity\Command\Adress;
use App\Entity\Command\Companydelivery;
use App\Entity\Command\Delivery;
use App\Entity\Command\PieceCommand;
use App\Entity\Command\TypeDelivery;
use App\Entity\Product\VariantProduct;
use App\Entity\User\User;


class CommandFixtures extends Fixture implements ContainerAwareInterface, DependentFixtureInterface
{

	private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

	public function load(ObjectManager $manager) 
	{

		//Création des Commands.

		$command1 = new Command();
		$command1->setCreatedAt(new DateTime('2020-06-07'));

		$command2 = new Command();
		$command2->setCreatedAt(new DateTime('2019-03-26'));

		$command3 = new Command();
		$command3->setCreatedAt(new DateTime('2019-11-12'));

		$command4 = new Command();
		$command4->setCreatedAt(new DateTime('2020-05-18'));

		$command5 = new Command();
		$command5->setCreatedAt(new DateTime('2019-12-16'));

		$command6 = new Command();
		$command6->setCreatedAt(new DateTime('2020-01-23'));

		$command7 = new Command();
		$command7->setCreatedAt(new DateTime('2020-03-06'));

		$command8 = new Command();

		//Récupération des VariantsProducts, des Users et des Adress.

		$research_variant_product = $this->container->get('doctrine')->getRepository(VariantProduct::class);
		$sachet_poireau = $research_variant_product->findBy(['code' => 'sachet_poireau'])[0];
		$pomme = $research_variant_product->findBy(['code' => 'pommes'])[0];
		$poire_conserve_500 = $research_variant_product->findBy(['code' => 'poire_conserve_500'])[0];
		$asperge = $research_variant_product->findBy(['code' => 'sachet_asperge'])[0];
		$sachet_poire = $research_variant_product->findBy(['code' => 'sachet_poire'])[0];
		$poire_tranchee = $research_variant_product->findBy(['code' => 'poire_tranchee'])[0];
		$soupe_legume_vert = $research_variant_product->findBy(['code' => 'soupe_legume_vert'])[0];
		$poireau_conserve = $research_variant_product->findBy(['code' => 'poireau_conserve'])[0];

		$research_user = $this->container->get('doctrine')->getRepository(User::class);
		$truc = $research_user->findBy(['username' => 'truc'])[0];
		$test1 = $research_user->findBy(['username' => 'test1'])[0];
		$test2 = $research_user->findBy(['username' => 'test2'])[0];
		$test3 = $research_user->findBy(['username' => 'test3'])[0];

		$research_adress = $this->container->get('doctrine')->getRepository(Adress::class);
		$adress2 = $research_adress->findOneBy(['city' => 'Strasbourg']);
		$adress4 = $research_adress->findOneBy(['city' => 'Paris']);

		//Création  et récupération d'adresse.

		$adress1 = new Adress();
        $adress1->setStreet('1 Allée du Vexin');
        $adress1->setZipCode('95180');
        $adress1->setCity('Menucourt');


		$adress3 = new Adress();
		$adress3->setStreet('19 Rue Géruzez');
		$adress3->setZipCode('51100');
		$adress3->setCity('Reims');

		$adress5 = new Adress();
		$adress5->setStreet('12 Rue Dubois Crancé');
		$adress5->setZipCode('08000');
		$adress5->setCity('Charleville-Mézières');

		$adress6 = new Adress();
		$adress6->setStreet('6 Rue Spielmann');
		$adress6->setZipCode('67000');
		$adress6->setCity('Strasbourg');

		//Création des CompaniesDeliveries.

		$companydelivery1 = new Companydelivery();
		$companydelivery1->setName('UPS');
		$companydelivery1->setArea(['All']);
		$companydelivery1->setLogoFileName('ups.jpeg');
		$companydelivery1->setActivate(true);

		$companydelivery2 = new Companydelivery();
		$companydelivery2->setName('Faux livreur Grand EST');
		$companydelivery2->setArea(['08', '10', '51', '52', '54', '55', '57', '67', '68', '88']);
		$companydelivery2->setActivate(true);

		//Création des TypeDeliveries.

		$typedelivery1 = new TypeDelivery();
		$typedelivery1->setName('UPR simple');
		$typedelivery1->setPrice(800);
		$typedelivery1->setTimeMin(3);
		$typedelivery1->setTimeMax(4);
		$typedelivery1->setActivate(true);

		$typedelivery2 = new TypeDelivery();
		$typedelivery2->setName('UPR rapide');
		$typedelivery2->setPrice(1200);
		$typedelivery2->setTimeMin(1);
		$typedelivery2->setTimeMax(2);
		$typedelivery2->setActivate(true);

		$typedelivery3 = new TypeDelivery();
		$typedelivery3->setName('Faux livreur simple');
		$typedelivery3->setPrice(700);
		$typedelivery3->setTimeMin(3);
		$typedelivery3->setTimeMax(4);
		$typedelivery3->setActivate(true);

		//Création des Deleveries.

		$delivery1 = new delivery();
		$delivery1->setDate(new DateTime('01-07-2020'));

		$delivery2 = new delivery();
		$delivery2->setDate(new DateTime('23-06-2020'));

		$delivery3 = new delivery();
		$delivery3->setDate(new DateTime('12-04-2020'));

		$delivery4 = new delivery();
		$delivery4->setDate(new DateTime('30-05-2020'));

		$delivery5 = new delivery();
		//$delivery5->setDate(new DateTime('12-05-2020'));

		$delivery6 = new delivery();
		$delivery6->setDate(new DateTime('16-07-2020'));

		//Création des liens entre les différents objets.

		$command1->setDelivery($delivery1);
		$command2->setDelivery($delivery2);
		$command3->setDelivery($delivery3);
		$command4->setDelivery($delivery4);
		$command5->setDelivery($delivery5);
		$command6->setDelivery($delivery6);

		$command1->setPlaceDel($adress1);
		$command2->setPlaceDel($adress2);
		$command3->setPlaceDel($adress3);
		$command4->setPlaceDel($adress4);
		$command5->setPlaceDel($adress5);
		$command6->setPlaceDel($adress6);

		$companydelivery1->addType($typedelivery1);
		$companydelivery1->addType($typedelivery2);
		$companydelivery2->addType($typedelivery3);

		$typedelivery1->addDelivery($delivery1);
		$typedelivery2->addDelivery($delivery3);
		$typedelivery2->addDelivery($delivery4);
		$typedelivery2->addDelivery($delivery5);
		$typedelivery2->addDelivery($delivery6);
		$typedelivery3->addDelivery($delivery2);

		//Création des PiecesCommands.

		$pieceCommand1 = new PieceCommand();
		$pieceCommand1->setNbProducts(2);
		$pieceCommand1->setProduct($sachet_poireau);
		$pieceCommand1->setCommand($command1);

		$pieceCommand2 = new PieceCommand();
		$pieceCommand2->setNbProducts(7);
		$pieceCommand2->setProduct($poireau_conserve);
		$pieceCommand2->setCommand($command2);

		$pieceCommand3 = new PieceCommand();
		$pieceCommand3->setNbProducts(6);
		$pieceCommand3->setProduct($poire_tranchee);
		$pieceCommand3->setCommand($command3);

		$pieceCommand4 = new PieceCommand();
		$pieceCommand4->setNbProducts(3);
		$pieceCommand4->setProduct($asperge);
		$pieceCommand4->setCommand($command4);

		$pieceCommand5 = new PieceCommand();
		$pieceCommand5->setNbProducts(4);
		$pieceCommand5->setProduct($sachet_poire);
		$pieceCommand5->setCommand($command5);

		$pieceCommand6 = new PieceCommand();
		$pieceCommand6->setNbProducts(6);
		$pieceCommand6->setProduct($soupe_legume_vert);
		$pieceCommand6->setCommand($command6);

		$pieceCommand7 = new PieceCommand();
		$pieceCommand7->setNbProducts(4);
		$pieceCommand7->setProduct($asperge);
		$pieceCommand7->setCommand($command7);

		$pieceCommand8 = new PieceCommand();
		$pieceCommand8->setNbProducts(3);
		$pieceCommand8->setProduct($poire_conserve_500);
		$pieceCommand8->setCommand($command8);

		$pieceCommand9 = new PieceCommand();
		$pieceCommand9->setNbProducts(2);
		$pieceCommand9->setProduct($pomme);
		$pieceCommand9->setCommand($command1);

		$pieceCommand10 = new PieceCommand();
		$pieceCommand10->setNbProducts(10);
		$pieceCommand10->setProduct($pomme);
		$pieceCommand10->setCommand($command2);

		$pieceCommand11 = new PieceCommand();
		$pieceCommand11->setNbProducts(6);
		$pieceCommand11->setProduct($poireau_conserve);
		$pieceCommand11->setCommand($command1);

		$pieceCommand12 = new PieceCommand();
		$pieceCommand12->setNbProducts(3);
		$pieceCommand12->setProduct($poire_conserve_500);
		$pieceCommand12->setCommand($command3);

		//Création des liens entre les Commandes et les Users.

		$truc->addCommand($command1);
		$truc->addCommand($command2);
		$truc->addCommand($command3);
		$test1->addCommand($command4);
		$test1->addCommand($command5);
		$test2->addCommand($command6);
		$test3->addCommand($command7);
		$test3->addCommand($command8);

		//Création des dates de réception.

		$command1->setDateReceive(new DateTime('16-07-2020'));
		$command2->setDateReceive(new DateTime('30-06-2020'));
		$command3->setDateReceive(new DateTime('26-04-2020'));
		$command4->setDateReceive(new DateTime('08-06-2020'));
		//$command5->setDateReceive(new DateTime('20-05-2020'));

		//Calcule des prix des Commands.

		$command1->setCompleted(true);
		$command2->setCompleted(true);
		$command3->setCompleted(true);
		$command4->setCompleted(true);
		$command5->setCompleted(true);
		$command6->setCompleted(true);

		$command1->calculPriceTotal();
		$command2->calculPriceTotal();
		$command3->calculPriceTotal();
		$command4->calculPriceTotal();
		$command5->calculPriceTotal();
		$command6->calculPriceTotal();

		//Sauvegarde des entités et des modifications effectuées.

		$manager->persist($command1);
		$manager->persist($command2);
		$manager->persist($command3);
		$manager->persist($command4);
		$manager->persist($command5);
		$manager->persist($command6);
		$manager->persist($command7);
		$manager->persist($command8);

		$manager->persist($adress1);
		$manager->persist($adress2);
		$manager->persist($adress3);
		$manager->persist($adress4);
		$manager->persist($adress5);
		$manager->persist($adress6);

		$manager->persist($companydelivery1);
		$manager->persist($companydelivery2);

		$manager->persist($typedelivery1);
		$manager->persist($typedelivery2);
		$manager->persist($typedelivery3);

		$manager->persist($delivery1);
		$manager->persist($delivery2);
		$manager->persist($delivery3);
		$manager->persist($delivery4);
		$manager->persist($delivery5);
		$manager->persist($delivery6);

		$manager->persist($pieceCommand1);
		$manager->persist($pieceCommand2);
		$manager->persist($pieceCommand3);
		$manager->persist($pieceCommand4);
		$manager->persist($pieceCommand5);
		$manager->persist($pieceCommand6);
		$manager->persist($pieceCommand7);
		$manager->persist($pieceCommand8);
		$manager->persist($pieceCommand9);
		$manager->persist($pieceCommand10);
		$manager->persist($pieceCommand11);
		$manager->persist($pieceCommand12);

		$manager->persist($sachet_poireau);
		$manager->persist($pomme);
		$manager->persist($poire_conserve_500);
		$manager->persist($asperge);
		$manager->persist($sachet_poire);
		$manager->persist($poire_tranchee);
		$manager->persist($soupe_legume_vert);
		$manager->persist($poireau_conserve);

		$manager->flush();
	}

	public function getDependencies()
	{
		return array(
			ProductFixtures::class,
			UserFixtures::class,
		);
	}

}


