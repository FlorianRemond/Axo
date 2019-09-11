<?php


namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class DateConnexionService
{
    //l'entité qui nous intéresse est réprésentée par entityClass
    private $entityClass;
    private $manager;
    //injection de dépendance pour savoir qu'elles sont les informations nécessaire pour construire le service
    public function __construct(ObjectManager $manager)
    {
     $this->manager=$manager;
    }
    public function setEntityClass($entityClass)
    {
        //permet de modifier l'entité
        $this->entityClass= $entityClass;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }
    public function getDateConnexion()
    {
        $repo = $this->manager->getRepository($this->entityClass);
        $datesConnexion = $repo ->findBy(['connectedAt']);

        $count = 0;
        $dateN = new \DateTime();
        $dateNow = $dateN->format('Y-m-d');
        foreach ($datesConnexion as $dateConnexion) {
            $dateCo = $dateConnexion['connectedAt']->format('Y-m-d');
            if ($dateCo == $dateNow) {
                $count++;
                echo $count;
            }
        }
    }
}

