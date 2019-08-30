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

        for ($i = 1; $i <= 5; $i++) {

            $title = $faker->sentence();
            $content = $faker->paragraph(40);
           // $image = $faker->imageUrl(500, 350);
             $imageName=$faker->name('image');
           // $imageFile=$faker->file('C:\Image','C:\Image2',false);
           // $imageFile=$faker->image(null, 500, 500, 'cats', true, true, 'Faker');
            $publishedAt = $faker->dateTimeBetween($startDate='+1 years', $endDate ='+2 years');
            $isPrivate=$faker->numberBetween(0,1);


            $article = new Article();
            $article
                ->setTitle(($title))
                ->setContent($content)
               // ->setImage($image)
               // ->setImageName($imageName)
                //->setImageFile($imageFile)
                ->setPublishedAt($publishedAt)
                ->setCreatedAt(new \DateTime())
                ->setIsPrivate($isPrivate);

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
