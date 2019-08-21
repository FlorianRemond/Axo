<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        for ($i = 1; $i <= 5; $i++) {
            $article = new Article();
            $title = $faker->sentence();
            $content = $faker->paragraph(40);
            $image = $faker->imageUrl(500, 350);
            $publishedAt = $faker->dateTimeBetween($startDate='+1 years', $endDate ='+2 years');


            $article->setTitle(($title))
                ->setContent($content)
                ->setImage($image)
                ->setPublishedAt($publishedAt)
                ->setCreatedAt(new \DateTime());

                        for($j =1; $j <=1; $j ++){
                            $image = new Image();
                            $image ->setUrl($faker -> imageUrl())
                                   ->setArt($article)
                                   ->setCaption($faker->sentence());
                            $manager->persist($image);
                        }
            $manager->persist($article);
        }
        $manager->flush();
    }
}
