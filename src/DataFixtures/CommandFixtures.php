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
use App\Entity\Command\Address;
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

		$command_u_truc_c_menucourt_v_sachet_poireau_pomme = new Command();
		$command_u_truc_c_menucourt_v_sachet_poireau_pomme->setCreatedAt(new DateTime('2020-06-07'));
		$command_u_truc_c_menucourt_v_sachet_poireau_pomme->setIsBasket(false);

		$command_u_truc_c_strasbourg_v_poireau_conserve_pomme = new Command();
		$command_u_truc_c_strasbourg_v_poireau_conserve_pomme->setCreatedAt(new DateTime('2019-03-26'));
		$command_u_truc_c_strasbourg_v_poireau_conserve_pomme->setIsBasket(false);

		$command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500 = new Command();
		$command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500->setCreatedAt(new DateTime('2019-11-12'));
		$command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500->setIsBasket(false);

		$command_u_test1_c_paris_v_asperge = new Command();
		$command_u_test1_c_paris_v_asperge->setCreatedAt(new DateTime('2020-05-18'));
		$command_u_test1_c_paris_v_asperge->setIsBasket(false);

		$command_u_test1_c_charleville_mezieres_v_sachet_poire = new Command();
		$command_u_test1_c_charleville_mezieres_v_sachet_poire->setCreatedAt(new DateTime('2019-12-16'));
		$command_u_test1_c_charleville_mezieres_v_sachet_poire->setIsBasket(false);

		$command_u_test2_c_strasbourg_v_soupe_legume_vert = new Command();
		$command_u_test2_c_strasbourg_v_soupe_legume_vert->setCreatedAt(new DateTime('2020-01-23'));
		$command_u_test2_c_strasbourg_v_soupe_legume_vert->setIsBasket(false);

		$command_u_test3_v_asperge = new Command();
		$command_u_test3_v_asperge->setCreatedAt(new DateTime('2020-03-06'));
		$command_u_test3_v_asperge->setIsBasket(false);

		$command_u_test3_v_poire_conserve_500 = new Command();

		$command_u_test4_c_poitiers_v_asperge_poireau_conserve = new Command();
		$command_u_test4_c_poitiers_v_asperge_poireau_conserve->setCreatedAt(new DateTime('2020-06-22'));
		$command_u_test4_c_poitiers_v_asperge_poireau_conserve->setIsBasket(false);

		//Récupération des VariantsProducts, des Users et des Address.

		$research_variant_product = $this->container->get('doctrine')->getRepository(VariantProduct::class);
		$sachet_poireau = $research_variant_product->findBy(['code' => 'sachet_poireau'])[0];
		$pomme = $research_variant_product->findBy(['code' => 'pommes'])[0];
		$poire_conserve_250 = $research_variant_product->findBy(['code' => 'poire_conserve_250'])[0];
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
		$test4 = $research_user->findBy(['username' => 'test4'])[0];

		$research_address = $this->container->get('doctrine')->getRepository(Address::class);
		$address2 = $research_address->findOneBy(['city' => 'Strasbourg']);
		$address4 = $research_address->findOneBy(['city' => 'Paris']);
		$address7 = $research_address->findOneBy(['city' => 'Poitiers']);

		//Création  et récupération d'adresse.

		$address1 = new Address();
        $address1->setStreet('1 Allée du Vexin');
        $address1->setZipCode('95180');
        $address1->setCity('Menucourt');

		$address3 = new Address();
		$address3->setStreet('19 Rue Géruzez');
		$address3->setZipCode('51100');
		$address3->setCity('Reims');

		$address5 = new Address();
		$address5->setStreet('12 Rue Dubois Crancé');
		$address5->setZipCode('08000');
		$address5->setCity('Charleville-Mézières');

		$address6 = new Address();
		$address6->setStreet('6 Rue Spielmann');
		$address6->setZipCode('67000');
		$address6->setCity('Strasbourg');

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
		$typedelivery1->setName('UPS simple');
		$typedelivery1->setPrice(800);
		$typedelivery1->setTimeMin(3);
		$typedelivery1->setTimeMax(4);
		$typedelivery1->setActivate(true);

		$typedelivery2 = new TypeDelivery();
		$typedelivery2->setName('UPS rapide');
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

		$delivery1 = new Delivery();
		$delivery1->setDate(new DateTime('01-07-2020'));

		$delivery2 = new Delivery();
		$delivery2->setDate(new DateTime('23-06-2020'));

		$delivery3 = new Delivery();
		$delivery3->setDate(new DateTime('12-04-2020'));

		$delivery4 = new Delivery();
		$delivery4->setDate(new DateTime('30-05-2020'));

		$delivery5 = new Delivery();
		//$delivery5->setDate(new DateTime('12-05-2020'));

		$delivery6 = new Delivery();
		$delivery6->setDate(new DateTime('16-07-2020'));

		$delivery7 = new Delivery();
		$delivery7->setDate(new DateTime('2020-06-28'));

		//Création des liens entre les différents objets.

		$command_u_truc_c_menucourt_v_sachet_poireau_pomme->setDelivery($delivery1);
		$command_u_truc_c_strasbourg_v_poireau_conserve_pomme->setDelivery($delivery2);
		$command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500->setDelivery($delivery3);
		$command_u_test1_c_paris_v_asperge->setDelivery($delivery4);
		$command_u_test1_c_charleville_mezieres_v_sachet_poire->setDelivery($delivery5);
		$command_u_test2_c_strasbourg_v_soupe_legume_vert->setDelivery($delivery6);
		$command_u_test4_c_poitiers_v_asperge_poireau_conserve->setDelivery($delivery7);

		$command_u_truc_c_menucourt_v_sachet_poireau_pomme->setPlaceDel($address1);
		$command_u_truc_c_strasbourg_v_poireau_conserve_pomme->setPlaceDel($address2);
		$command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500->setPlaceDel($address3);
		$command_u_test1_c_paris_v_asperge->setPlaceDel($address4);
		$command_u_test1_c_charleville_mezieres_v_sachet_poire->setPlaceDel($address5);
		$command_u_test2_c_strasbourg_v_soupe_legume_vert->setPlaceDel($address6);
		$command_u_test4_c_poitiers_v_asperge_poireau_conserve->setPlaceDel($address7);

		$command_u_truc_c_menucourt_v_sachet_poireau_pomme->setTypeDelSelected($typedelivery1);
		$command_u_truc_c_strasbourg_v_poireau_conserve_pomme->setTypeDelSelected($typedelivery3);
		$command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500->setTypeDelSelected($typedelivery2);
		$command_u_test1_c_paris_v_asperge->setTypeDelSelected($typedelivery2);
		$command_u_test1_c_charleville_mezieres_v_sachet_poire->setTypeDelSelected($typedelivery2);
		$command_u_test2_c_strasbourg_v_soupe_legume_vert->setTypeDelSelected($typedelivery2);
		$command_u_test4_c_poitiers_v_asperge_poireau_conserve->setTypeDelSelected($typedelivery1);

		$companydelivery1->addType($typedelivery1);
		$companydelivery1->addType($typedelivery2);
		$companydelivery2->addType($typedelivery3);

		$typedelivery1->addDelivery($delivery1);
		$typedelivery1->addDelivery($delivery7);
		$typedelivery2->addDelivery($delivery3);
		$typedelivery2->addDelivery($delivery4);
		$typedelivery2->addDelivery($delivery5);
		$typedelivery2->addDelivery($delivery6);
		$typedelivery3->addDelivery($delivery2);

		//Création des PiecesCommands.

		$pieceCommand1 = new PieceCommand();
		$pieceCommand1->setNbProducts(2);
		$pieceCommand1->setProduct($sachet_poireau);
		$pieceCommand1->setCommand($command_u_truc_c_menucourt_v_sachet_poireau_pomme);

		$pieceCommand2 = new PieceCommand();
		$pieceCommand2->setNbProducts(7);
		$pieceCommand2->setProduct($poireau_conserve);
		$pieceCommand2->setCommand($command_u_truc_c_strasbourg_v_poireau_conserve_pomme);

		$pieceCommand3 = new PieceCommand();
		$pieceCommand3->setNbProducts(6);
		$pieceCommand3->setProduct($poire_tranchee);
		$pieceCommand3->setCommand($command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500);

		$pieceCommand4 = new PieceCommand();
		$pieceCommand4->setNbProducts(3);
		$pieceCommand4->setProduct($asperge);
		$pieceCommand4->setCommand($command_u_test1_c_paris_v_asperge);

		$pieceCommand5 = new PieceCommand();
		$pieceCommand5->setNbProducts(4);
		$pieceCommand5->setProduct($sachet_poire);
		$pieceCommand5->setCommand($command_u_test1_c_charleville_mezieres_v_sachet_poire);

		$pieceCommand6 = new PieceCommand();
		$pieceCommand6->setNbProducts(6);
		$pieceCommand6->setProduct($soupe_legume_vert);
		$pieceCommand6->setCommand($command_u_test2_c_strasbourg_v_soupe_legume_vert);

		$pieceCommand7 = new PieceCommand();
		$pieceCommand7->setNbProducts(4);
		$pieceCommand7->setProduct($asperge);
		$pieceCommand7->setCommand($command_u_test3_v_asperge);

		$pieceCommand8 = new PieceCommand();
		$pieceCommand8->setNbProducts(3);
		$pieceCommand8->setProduct($poire_conserve_500);
		$pieceCommand8->setCommand($command_u_test3_v_poire_conserve_500);

		$pieceCommand9 = new PieceCommand();
		$pieceCommand9->setNbProducts(2);
		$pieceCommand9->setProduct($pomme);
		$pieceCommand9->setCommand($command_u_truc_c_menucourt_v_sachet_poireau_pomme);

		$pieceCommand10 = new PieceCommand();
		$pieceCommand10->setNbProducts(10);
		$pieceCommand10->setProduct($pomme);
		$pieceCommand10->setCommand($command_u_truc_c_strasbourg_v_poireau_conserve_pomme);

		$pieceCommand11 = new PieceCommand();
		$pieceCommand11->setNbProducts(6);
		$pieceCommand11->setProduct($poireau_conserve);
		$pieceCommand11->setCommand($command_u_truc_c_menucourt_v_sachet_poireau_pomme);

		$pieceCommand12 = new PieceCommand();
		$pieceCommand12->setNbProducts(3);
		$pieceCommand12->setProduct($poire_conserve_500);
		$pieceCommand12->setCommand($command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500);

		$pieceCommand13 = new PieceCommand();
		$pieceCommand13->setNbProducts(4);
		$pieceCommand13->setProduct($asperge);
		$pieceCommand13->setCommand($command_u_test4_c_poitiers_v_asperge_poireau_conserve);

		$pieceCommand14 = new PieceCommand();
		$pieceCommand14->setNbProducts(2);
		$pieceCommand14->setProduct($poireau_conserve);
		$pieceCommand14->setCommand($command_u_test4_c_poitiers_v_asperge_poireau_conserve);

		//Création des liens entre les Commandes et les Users.

		$truc->addCommand($command_u_truc_c_menucourt_v_sachet_poireau_pomme);
		$truc->addCommand($command_u_truc_c_strasbourg_v_poireau_conserve_pomme);
		$truc->addCommand($command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500);
		$test1->addCommand($command_u_test1_c_paris_v_asperge);
		$test1->addCommand($command_u_test1_c_charleville_mezieres_v_sachet_poire);
		$test2->addCommand($command_u_test2_c_strasbourg_v_soupe_legume_vert);
		$test3->addCommand($command_u_test3_v_asperge);
		$test3->addCommand($command_u_test3_v_poire_conserve_500);
		$test4->addCommand($command_u_test4_c_poitiers_v_asperge_poireau_conserve);

		//Création des dates de réception.

		$command_u_truc_c_menucourt_v_sachet_poireau_pomme->setDateReceive(new DateTime('16-07-2020'));
		$command_u_truc_c_strasbourg_v_poireau_conserve_pomme->setDateReceive(new DateTime('30-06-2020'));
		$command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500->setDateReceive(new DateTime('26-04-2020'));
		$command_u_test1_c_paris_v_asperge->setDateReceive(new DateTime('08-06-2020'));
		//$command_u_test1_c_charleville_mezieres_v_sachet_poire->setDateReceive(new DateTime('20-05-2020'));
		$command_u_test4_c_poitiers_v_asperge_poireau_conserve->setDateReceive(new DateTime('2020-06-28'));

		//Calcule des prix des Commands.

		$command_u_truc_c_menucourt_v_sachet_poireau_pomme->setCompleted(true);
		$command_u_truc_c_strasbourg_v_poireau_conserve_pomme->setCompleted(true);
		$command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500->setCompleted(true);
		$command_u_test1_c_paris_v_asperge->setCompleted(true);
		$command_u_test1_c_charleville_mezieres_v_sachet_poire->setCompleted(true);
		$command_u_test2_c_strasbourg_v_soupe_legume_vert->setCompleted(true);
		$command_u_test4_c_poitiers_v_asperge_poireau_conserve->setCompleted(true);

		$command_u_truc_c_menucourt_v_sachet_poireau_pomme->calculPriceTotal();
		$command_u_truc_c_strasbourg_v_poireau_conserve_pomme->calculPriceTotal();
		$command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500->calculPriceTotal();
		$command_u_test1_c_paris_v_asperge->calculPriceTotal();
		$command_u_test1_c_charleville_mezieres_v_sachet_poire->calculPriceTotal();
		$command_u_test2_c_strasbourg_v_soupe_legume_vert->calculPriceTotal();
		$command_u_test4_c_poitiers_v_asperge_poireau_conserve->calculPriceTotal();

		//Sauvegarde des entités et des modifications effectuées.

		$manager->persist($command_u_truc_c_menucourt_v_sachet_poireau_pomme);
		$manager->persist($command_u_truc_c_strasbourg_v_poireau_conserve_pomme);
		$manager->persist($command_u_truc_c_reims_v_poire_tranchee_poire_conserve_500);
		$manager->persist($command_u_test1_c_paris_v_asperge);
		$manager->persist($command_u_test1_c_charleville_mezieres_v_sachet_poire);
		$manager->persist($command_u_test2_c_strasbourg_v_soupe_legume_vert);
		$manager->persist($command_u_test3_v_asperge);
		$manager->persist($command_u_test3_v_poire_conserve_500);
		$manager->persist($command_u_test4_c_poitiers_v_asperge_poireau_conserve);

		$manager->persist($address1);
		$manager->persist($address2);
		$manager->persist($address3);
		$manager->persist($address4);
		$manager->persist($address5);
		$manager->persist($address6);
		$manager->persist($address7);

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
		$manager->persist($delivery7);

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
		$manager->persist($pieceCommand13);
		$manager->persist($pieceCommand14);

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
