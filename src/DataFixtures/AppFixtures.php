<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Role;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder){
        $this ->encoder =$encoder;
    }

    public function load(ObjectManager $manager)
    {
        /*
        // $product = new Product();
        // $manager->persist($product);
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setUsername('Florian')
                  ->setEmail('florian@axocap.com')
                  ->setPassword($this->encoder->encodePassword($adminUser,'password'))
                  ->addUserRole($adminRole)  ;
        $manager->persist($adminUser);

        $manager->flush();
        */
    }

}
