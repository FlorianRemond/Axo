<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder){
        $this ->encoder =$encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr-FR');
        for($i=0; $i <=105; $i++) {

            $username=$faker->name();
            $email = $faker->safeEmail;
            $society =$faker ->company;
            $connectedAt = $faker->dateTime('now');

            $user = new User();
            $user->setUsername($username)
                ->setPassword($this->encoder->encodePassword($user,'password'))
                ->setEmail($email)
                ->setSociety($society)
                ->setCreatedAt(new \DateTime())
                ->setConnectedAt($connectedAt);

            $manager->persist($user);
        }
        $manager->flush();

    }

}
