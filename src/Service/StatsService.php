<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class StatsService{
    private $manager;

    public function _construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function getUsersCount(){
    $this-> $manager ->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getArticleCount(){
        $this-> $manager ->createQuery('SELECT COUNT(a) FROM App\Entity\Article a')->getSingleScalarResult();
    }

}