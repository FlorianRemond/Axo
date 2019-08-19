<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;


class StatsService{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function getUsersCount(){
    $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getArticlesCount(){
        $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Article a')->getSingleScalarResult();
    }

    public function getStats(){
        $articles = $this->getArticlesCount();
        $users = $this->getUsersCount();
        return compact('articles','users');
    }

}