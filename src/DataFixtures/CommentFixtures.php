<?php

namespace App\DataFixtures;

use DateTime;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\DataFixtures\ProductFixtures;
use App\DataFixtures\UserFixtures;

use App\Entity\Product\VariantProduct;
use App\Entity\User\User;
use App\Entity\User\Comment;


class CommentFixtures extends Fixture implements ContainerAwareInterface, DependentFixtureInterface
{

	private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

	public function load(ObjectManager $manager) 
	{
		//Création des Comment.

		$comment1 = new Comment();
		$comment2 = new Comment();
		$comment3 = new Comment();
		$comment4 = new Comment();
		$comment5 = new Comment();
		$comment6 = new Comment();
		$comment7 = new Comment();
		$comment8 = new Comment();

		$comment1->setMark(4);
		$comment2->setMark(4);
		$comment3->setMark(5);
		$comment4->setMark(3);
		$comment5->setMark(3);
		$comment6->setMark(2);
		$comment7->setMark(1);
		$comment8->setMark(4);

		$comment1->setCreatedAt(new DateTime('2020-07-01 14:22'));
		$comment2->setCreatedAt(new DateTime('2019-04-03 08:13'));
		$comment3->setCreatedAt(new DateTime('2019-11-18 16:04'));
		$comment4->setCreatedAt(new DateTime('2020-05-26 18:36'));
		$comment5->setCreatedAt(new DateTime('2019-12-28 11:38'));
		$comment6->setCreatedAt(new DateTime('2020-02-01 09:47'));
		$comment7->setCreatedAt(new DateTime('2020-08-07 10:44'));
		$comment8->setCreatedAt(new DateTime('2019-04-03 08:9'));

		$comment1->setText("Ce sachet de poireaux est très bon.");
		$comment2->setText("Les poireaux en conserve sont très bon.");
		$comment3->setText("Les poires tranchés sont très bon.");
		$comment4->setText("Les asperges sont très bonnes.");
		$comment5->setText("Les poires ne sont pas mauvaise.");
		$comment6->setText("La soupe manque de goût.");
		$comment7->setText("Les asperges sont toujours pas arrivées.");
		$comment8->setText("Ce sachet de pomme est très bon.");

		//Récupération des VariantsProducts.

		$research_variant_product = $this->container->get('doctrine')->getRepository(VariantProduct::class);
		$sachet_poireau = $research_variant_product->findBy(['code' => 'sachet_poireau'])[0];
		$pomme = $research_variant_product->findBy(['code' => 'pommes'])[0];
		$asperge = $research_variant_product->findBy(['code' => 'sachet_asperge'])[0];
		$sachet_poire = $research_variant_product->findBy(['code' => 'sachet_poire'])[0];
		$poire_tranchee = $research_variant_product->findBy(['code' => 'poire_tranchee'])[0];
		$soupe_legume_vert = $research_variant_product->findBy(['code' => 'soupe_legume_vert'])[0];
		$poireau_conserve = $research_variant_product->findBy(['code' => 'poireau_conserve'])[0];

		//Récupération des Users.

		$research_user = $this->container->get('doctrine')->getRepository(User::class);
		$truc = $research_user->findBy(['username' => 'truc'])[0];
		$test1 = $research_user->findBy(['username' => 'test1'])[0];
		$test2 = $research_user->findBy(['username' => 'test2'])[0];
		$test3 = $research_user->findBy(['username' => 'test3'])[0];

		//Création des liens entre les différents objets.

		$truc->addComment($comment1);
		$sachet_poireau->addComment($comment1);

		$truc->addComment($comment2);
		$poireau_conserve->addComment($comment2);

		$truc->addComment($comment3);
		$poire_tranchee->addComment($comment3);

		$test1->addComment($comment4);
		$asperge->addComment($comment4);

		$test1->addComment($comment5);
		$sachet_poire->addComment($comment5);

		$test2->addComment($comment6);
		$soupe_legume_vert->addComment($comment6);

		$test3->addComment($comment7);
		$asperge->addComment($comment7);

		$truc->addComment($comment8);
		$pomme->addComment($comment8);

		//Sauvegarde des modifications effectuées.

		$manager->persist($comment1);
		$manager->persist($comment2);
		$manager->persist($comment3);
		$manager->persist($comment4);
		$manager->persist($comment5);
		$manager->persist($comment6);
		$manager->persist($comment7);
		$manager->persist($comment8);

		$manager->persist($sachet_poireau);
		$manager->persist($pomme);
		$manager->persist($asperge);
		$manager->persist($sachet_poire);
		$manager->persist($poire_tranchee);
		$manager->persist($soupe_legume_vert);
		$manager->persist($poireau_conserve);

		$manager->persist($truc);
		$manager->persist($test1);
		$manager->persist($test2);
		$manager->persist($test3);

		$manager->flush();
	}

	public function getDependencies()
	{
		return array(
			ProductFixtures::class,
			UserFixtures::class,
			CommandFixtures::class
		);
	}

}