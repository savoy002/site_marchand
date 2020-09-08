<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User\User;
use App\Entity\Command\Adress;

class UserFixtures extends Fixture
{

	private $passwordEncoder;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
		$this->passwordEncoder = $passwordEncoder;
	}

    public function load(ObjectManager $manager)
    {
        //Création des utilisateurs.

    	$user = new User();
    	$user->setUsername('invite');
    	$user->setEmail('invite@site_marchand.fr');
    	$user->setRoles(array("ROLE_USER"));
        $user->setAdmin(true);
    	$user->setPassword($this->passwordEncoder->encodePassword($user, 'invite'));
    	$user->setValid(true);

    	$userTest = new User();
    	$userTest->setUsername('truc');
    	$userTest->setEmail('bidon@gmail.com');
    	$userTest->setRoles(['ROLE_USER']);
        $userTest->setAdmin(true);
    	$userTest->setPassword($this->passwordEncoder->encodePassword($userTest, 'machin'));
        $userTest->setValid(true);
    	
    	$admin = new User();
    	$admin->setUsername('admin');
    	$admin->setEmail('admin@site_marchand.fr');
    	$admin->setRoles(['ROLE_ADMIN']);
        $admin->setSuperAdmin(true);
        $admin->setAdmin(true);
    	$admin->setPassword($this->passwordEncoder->encodePassword($admin, 'bidule'));
    	$admin->setValid(true);

        $test1 = new User();
        $test1->setUsername('test1');
        $test1->setEmail('test1@test.com');
        $test1->setRoles(['ROLE_USER']);
        $test1->setAdmin(true);
        $test1->setPassword($this->passwordEncoder->encodePassword($test1, 'test1'));
        $test1->setValid(false);

        $test2 = new User();
        $test2->setUsername('test2');
        $test2->setEmail('test2@test.com');
        $test2->setRoles(['ROLE_USER']);
        $test2->setAdmin(true);
        $test2->setPassword($this->passwordEncoder->encodePassword($test2, 'test2'));
        $test2->setValid(false);

        $test3 = new User();
        $test3->setUsername('test3');
        $test3->setEmail('test3@test.com');
        $test3->setRoles(['ROLE_USER']);
        $test3->setAdmin(true);
        $test3->setPassword($this->passwordEncoder->encodePassword($test3, 'test3'));
        $test3->setValid(false);

        $test4 = new User();
        $test4->setUsername('test4');
        $test4->setEmail('test4@test.com');
        $test4->setRoles(['ROLE_USER']);
        $test4->setAdmin(true);
        $test4->setPassword($this->passwordEncoder->encodePassword($test4, 'test4'));
        $test4->setValid(false);

        $test5 = new User();
        $test5->setUsername('test5');
        $test5->setEmail('test5@test.com');
        $test5->setRoles(['ROLE_USER']);
        $test5->setAdmin(true);
        $test5->setPassword($this->passwordEncoder->encodePassword($test5, 'test5'));
        $test5->setValid(false);

        //Création des Adress.

        $adress2 = new Adress();
        $adress2->setStreet('5 Rue des Boeufs');
        $adress2->setZipCode('67000');
        $adress2->setCity('Strasbourg');

        $adress4 = new Adress();
        $adress4->setStreet('7 Rue du Champ de Mars');
        $adress4->setZipCode('75007');
        $adress4->setCity('Paris');

        //Création des liens entre les Adress et les Users.

        $adress2->addBelong($userTest);
        $adress4->addBelong($test1);

        //Sauvegarde des données.
    	
    	$manager->persist($user);
    	$manager->persist($userTest);
    	$manager->persist($admin);

        $manager->persist($adress2);
        $manager->persist($adress4);

        $manager->persist($test1);
        $manager->persist($test2);
        $manager->persist($test3);
        $manager->persist($test4);
        $manager->persist($test5);

        $manager->flush();
    }
}
