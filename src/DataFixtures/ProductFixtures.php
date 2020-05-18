<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Product\Category;
use App\Entity\Product\Product;
use App\Entity\Product\VariantProduct;


class ProductFixtures extends Fixture
{

	public function load(ObjectManager $manager) 
	{

		//Objet de classe Product.

		$poire = new Product();
		$poire->setName('Poire');
		$poire->setDescription("");
		$poire->setCode('poire');
		$poire->setActivate(true);

		$pomme = new Product();
		$pomme->setName('Pomme');
		$pomme->setDescription("");
		$pomme->setCode('pomme');
		$pomme->setActivate(true);

		$soupe_de_legume_vert = new Product();
		$soupe_de_legume_vert->setName('Soupe de légume vert');
		$soupe_de_legume_vert->setDescription("");
		$soupe_de_legume_vert->setCode('soupe_de_legume_vert');
		$soupe_de_legume_vert->setActivate(true);

		$betrave = new Product();
		$betrave->setName('Betrave');
		$betrave->setDescription("");
		$betrave->setCode('betrave');
		$betrave->setActivate(true);

		$poireau = new Product();
		$poireau->setName('Poireau');
		$poireau->setDescription("");
		$poireau->setCode('poireau');
		$poireau->setActivate(true);

		$asperge = new Product();
		$asperge->setName('Asperge');
		$asperge->setDescription("");
		$asperge->setCode('asperge');
		$asperge->setActivate(true);

		//Objet de classe Category.

		$fruit = new Category();
		$fruit->setName('Fruit');
		$fruit->setCode('fruit');
		$fruit->setActivate(true);

		$legume = new Category();
		$legume->setName('Légume');
		$legume->setCode('legume');
		$legume->setActivate(true);

		$conserve = new Category();
		$conserve->setName('En boîte de concerve');
		$conserve->setCode('boite_concerve');
		$conserve->setActivate(true);

		$soupe = new Category();
		$soupe->setName('Soupe');
		$soupe->setCode('soupe');
		$soupe->setActivate(true);

		//Objet de classe VariantProduct.

		$poire_conserve1 = new VariantProduct();
		$poire_conserve1->setName("Poire en conserve 250g");
		$poire_conserve1->setDescription("Une boîte de conserve de 250g de poire.");
		$poire_conserve1->setStock(3);
		$poire_conserve1->setCode('poire_conserve_250');
		$poire_conserve1->setPrice(200);
		$poire_conserve1->setActivate(true);

		$poire_conserve2 = new VariantProduct();
		$poire_conserve2->setName("Poire en conserve 500g");
		$poire_conserve2->setDescription("Une boîte de conserve de 500g de poire.");
		$poire_conserve2->setStock(5);
		$poire_conserve2->setCode('poire_conserve_500');
		$poire_conserve2->setPrice(360);
		$poire_conserve2->setActivate(true);

		$sachet_poire = new VariantProduct();
		$sachet_poire->setName("Sachet de poire");
		$sachet_poire->setDescription("Un sachet de poire frais de 1kg.");
		$sachet_poire->setStock(4);
		$sachet_poire->setCode('sachet_poire');
		$sachet_poire->setPrice(900);
		$sachet_poire->setActivate(true);

		$poire_tranchee = new VariantProduct();
		$poire_tranchee->setName("Poire tranchée");
		$poire_tranchee->setDescription("Une boîte de poire tranchée de 500g.");
		$poire_tranchee->setStock(10);
		$poire_tranchee->setCode('poire_tranchee');
		$poire_tranchee->setPrice(500);
		$poire_tranchee->setActivate(true);

		$sachet_pomme = new VariantProduct();
		$sachet_pomme->setName("Pommes");
		$sachet_pomme->setDescription("Un sachet de pommes de 500g.");
		$sachet_pomme->setStock(6);
		$sachet_pomme->setCode('pommes');
		$sachet_pomme->setPrice(400);
		$sachet_pomme->setActivate(true);

		$sachet_poireau = new VariantProduct();
		$sachet_poireau->setName("Sachet de poireau");
		$sachet_poireau->setDescription("Un sachet de poieaux de 500g.");
		$sachet_poireau->setStock(12);
		$sachet_poireau->setCode('sachet_poireau');
		$sachet_poireau->setPrice(800);
		$sachet_poireau->setActivate(true);

		$poireau_conserve = new VariantProduct();
		$poireau_conserve->setName("Poireau en conserve");
		$poireau_conserve->setDescription("Une boîte de conserve de 500g.");
		$poireau_conserve->setStock(6);
		$poireau_conserve->setCode('poireau_conserve');
		$poireau_conserve->setPrice(600);
		$poireau_conserve->setActivate(true);

		$sachet_asperge = new VariantProduct();
		$sachet_asperge->setName("Sachet d'asperge");
		$sachet_asperge->setDescription("Un sachet d'asperge de 500g");
		$sachet_asperge->setStock(20);
		$sachet_asperge->setCode('sachet_asperge');
		$sachet_asperge->setPrice(500);
		$sachet_asperge->setActivate(true);

		$soupe_de_legume_vert_bocale = new VariantProduct();
		$soupe_de_legume_vert_bocale->setName("Soupe de légume vert");
		$soupe_de_legume_vert_bocale->setDescription("Un bocale de soupe de légume vert de 1L.");
		$soupe_de_legume_vert_bocale->setStock(6);
		$soupe_de_legume_vert_bocale->setCode('soupe_legume_vert');
		$soupe_de_legume_vert_bocale->setPrice(400);
		$soupe_de_legume_vert_bocale->setActivate(true);

		//Création des liens entre les catégories et les produits.

		$fruit->addProduct($poire);
		$fruit->addProduct($pomme);
		$legume->addProduct($soupe_de_legume_vert);
		$legume->addProduct($betrave);
		$legume->addProduct($poireau);
		$legume->addProduct($asperge);
		$soupe->addProduct($soupe_de_legume_vert);

		//Création des liens entre les produits et les variantes de produit.

		$poire->addVariantProduct($poire_conserve1);
		$poire->addVariantProduct($poire_conserve2);
		$poire->addVariantProduct($poire_tranchee);
		$poire->addVariantProduct($sachet_poire);
		$pomme->addVariantProduct($sachet_pomme);
		$soupe_de_legume_vert->addVariantProduct($soupe_de_legume_vert_bocale);
		$poireau->addVariantProduct($sachet_poireau);
		$poireau->addVariantProduct($poireau_conserve);
		$asperge->addVariantProduct($sachet_asperge);

		//Création des liens entre les catégories et les variantes de produit.

		$fruit->addVariantProduct($poire_conserve1);
		$fruit->addVariantProduct($poire_conserve2);
		$fruit->addVariantProduct($sachet_poire);
		$fruit->addVariantProduct($poire_tranchee);
		$fruit->addVariantProduct($sachet_pomme);

		$legume->addVariantProduct($sachet_poireau);
		$legume->addVariantProduct($poireau_conserve);
		$legume->addVariantProduct($sachet_asperge);
		$legume->addVariantProduct($soupe_de_legume_vert_bocale);

		$conserve->addVariantProduct($poire_conserve1);
		$conserve->addVariantProduct($poire_conserve2);
		$conserve->addVariantProduct($poireau_conserve);

		$soupe->addVariantProduct($soupe_de_legume_vert_bocale);

		//Calcul des stocks des produits.

		$poire->calculStock();
		$pomme->calculStock();
		$soupe_de_legume_vert->calculStock();
		$betrave->calculStock();
		$poireau->calculStock();
		$asperge->calculStock();

		//Enregistrement des objets à la base de données.

		$manager->persist($poire);
		$manager->persist($pomme);
		$manager->persist($soupe_de_legume_vert);
		$manager->persist($betrave);
		$manager->persist($poireau);
		$manager->persist($asperge);

		$manager->persist($fruit);
		$manager->persist($legume);
		$manager->persist($conserve);
		$manager->persist($soupe);

		$manager->persist($poire_conserve1);
		$manager->persist($poire_conserve2);
		$manager->persist($sachet_poire);
		$manager->persist($poire_tranchee);
		$manager->persist($sachet_pomme);
		$manager->persist($sachet_poireau);
		$manager->persist($poireau_conserve);
		$manager->persist($sachet_asperge);
		$manager->persist($soupe_de_legume_vert_bocale);

		$manager->flush();
	}

}
