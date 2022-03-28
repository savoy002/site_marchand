<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\User\User;
use App\Entity\Command\Address;

class UserFixtures extends Fixture
{

	private $passwordHasher;

	public function __construct(UserPasswordHasherInterface $passwordHasher) {
		$this->passwordHasher = $passwordHasher;
	}

    public function load(ObjectManager $manager)
    {
        //Création des utilisateurs.

    	$user = new User();
    	$user->setUsername('invite');
    	$user->setEmail('invite@site_marchand.fr');
    	$user->setRoles(array("ROLE_USER"));
        $user->setAdmin(true);
    	$user->setPassword($this->passwordHasher->hashPassword($user, 'invite'));
    	$user->setValid(true);

    	$userTest = new User();
    	$userTest->setUsername('truc');
    	$userTest->setEmail('bidon@gmail.com');
    	$userTest->setRoles(['ROLE_USER']);
        $userTest->setAdmin(true);
    	$userTest->setPassword($this->passwordHasher->hashPassword($userTest, 'machin'));
        $userTest->setValid(true);
    	
    	$admin = new User();
    	$admin->setUsername('admin');
    	$admin->setEmail('admin@site_marchand.fr');
    	$admin->setRoles(['ROLE_ADMIN']);
        $admin->setSuperAdmin(true);
        $admin->setAdmin(true);
    	$admin->setPassword($this->passwordHasher->hashPassword($admin, 'bidule'));
    	$admin->setValid(true);

        $test1 = new User();
        $test1->setUsername('test1');
        $test1->setEmail('test1@test.com');
        $test1->setRoles(['ROLE_USER']);
        $test1->setAdmin(true);
        $test1->setPassword($this->passwordHasher->hashPassword($test1, 'test1'));
        $test1->setValid(true);

        $test2 = new User();
        $test2->setUsername('test2');
        $test2->setEmail('test2@test.com');
        $test2->setRoles(['ROLE_USER']);
        $test2->setAdmin(true);
        $test2->setPassword($this->passwordHasher->hashPassword($test2, 'test2'));
        $test2->setValid(true);

        $test3 = new User();
        $test3->setUsername('test3');
        $test3->setEmail('test3@test.com');
        $test3->setRoles(['ROLE_USER']);
        $test3->setAdmin(true);
        $test3->setPassword($this->passwordHasher->hashPassword($test3, 'test3'));
        $test3->setValid(true);

        $test4 = new User();
        $test4->setUsername('test4');
        $test4->setEmail('test4@test.com');
        $test4->setRoles(['ROLE_USER']);
        $test4->setAdmin(true);
        $test4->setPassword($this->passwordHasher->hashPassword($test4, 'test4'));
        $test4->setValid(true);

        $test5 = new User();
        $test5->setUsername('test5');
        $test5->setEmail('test5@test.com');
        $test5->setRoles(['ROLE_USER']);
        $test5->setAdmin(true);
        $test5->setPassword($this->passwordHasher->hashPassword($test5, 'test5'));
        $test5->setValid(false);

        $ups = new User();
        $ups->setUsername('UPS');
        $ups->setEmail('fausseadressemail@ups.com');
        $ups->setRoles(['ROLE_COMPANY_ADMIN']);
        $ups->setPassword($this->passwordHasher->hashPassword($ups, 'trucups'));
        $ups->setValid(true);

        //Création des Address.

        $address2 = new Address();
        $address2->setStreet('5 Rue des Boeufs');
        $address2->setZipCode('67000');
        $address2->setCity('Strasbourg');

        $address4 = new Address();
        $address4->setStreet('7 Rue du Champ de Mars');
        $address4->setZipCode('75007');
        $address4->setCity('Paris');

        $address7 = new Address();
        $address7->setStreet('3 Rue Riffault');
        $address7->setZipCode('86000');
        $address7->setCity('Poitiers');

        //Création des liens entre les Address et les Users.

        $address2->addBelong($userTest);
        $address4->addBelong($test1);
        $address7->addBelong($test4);

        //Sauvegarde des données.
    	
    	$manager->persist($user);
    	$manager->persist($userTest);
    	$manager->persist($admin);

        $manager->persist($address2);
        $manager->persist($address4);
        $manager->persist($address7);

        $manager->persist($test1);
        $manager->persist($test2);
        $manager->persist($test3);
        $manager->persist($test4);
        $manager->persist($test5);
        $manager->persist($ups);

        $manager->flush();
    }
}
