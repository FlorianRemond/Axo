<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        for ($i = 1; $i <= 50; $i++) {

            $title = $faker->sentence();
            $content = $faker->paragraph(40);
           // $imageName = $faker->imageUrl(500, 350);
            //$imageName=$faker->file('C:\Image','C:\Image2',false);
            $imageName=$faker->image(null, 500, 500, 'cats', true, true, 'Faker');
            $publishedAt = $faker->dateTimeBetween($startDate='+1 years', $endDate ='+2 years');
            $isPrivate=$faker->numberBetween(0,1);


            $article=New Article();
            $article->setTitle($title)
                    ->setCreatedAt(new \DateTime())
                    ->setContent($content)
                    ->setPublishedAt($publishedAt)
                    ->setIsPrivate($isPrivate)
                    ->setImageName($imageName);
            $manager->persist($article);
        }
        $manager->flush();
    }
}
