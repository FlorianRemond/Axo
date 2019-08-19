<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for ($i =1; $i <=100; $i ++){
            $article = new Article();
            $article -> setTitle("titre de l'article n°$i")
                     -> setContent ("<p>Contenu de l'article n$i</p>")
                     -> setImage ("http://placehold.it/350x150")
                     -> setCreatedAt(new \DateTime());
         $manager ->persist($article);
        }
        $manager->flush();
    }
}
