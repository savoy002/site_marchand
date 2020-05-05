<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User\User;

class UserFixtures extends Fixture
{

	private $passwordEncoder;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
		$this->passwordEncoder = $passwordEncoder;
	}

    public function load(ObjectManager $manager)
    {
    	$user = new User();
    	$user->setUsername('invite');
    	$user->setEmail('invite@site_marchand.fr');
    	$user->setRoles(array("ROLE_USER"));
        $admin->setAdmin(true);
    	$user->setPassword($this->passwordEncoder->encodePassword($user, 'invite'));
    	$user->setValid(true);

    	$userTest = new User();
    	$userTest->setUsername('truc');
    	$userTest->setEmail('bidon@gmail.com');
    	$userTest->setRoles(['ROLE_USER']);
        $admin->setAdmin(true);
    	$userTest->setPassword($this->passwordEncoder->encodePassword($userTest, 'machin'));
    	
    	$admin = new User();
    	$admin->setUsername('admin');
    	$admin->setEmail('admin@site_marchand.fr');
    	$admin->setRoles(['ROLE_ADMIN']);
        $admin->setSuperAdmin(true);
        $admin->setAdmin(true);
    	$admin->setPassword($this->passwordEncoder->encodePassword($admin, 'bidule'));
    	$admin->setValid(true);
    	
    	$manager->persist($user);
    	$manager->persist($userTest);
    	$manager->persist($admin);

        $manager->flush();
    }
}
