<?php

namespace App\Controller;


use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StatService;
use App\Repository\UserRepository;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     *
     */
    public function index(ObjectManager $manager,StatService $statService, UserRepository $userRepository){


       // $statService->readStats();
       //$statService->getConnexion();


        //ici on compte les articles en passant par une requete DQL et plutot que d'avoir les résultats sous forme de
        //tableau , la fonction ScalarResult affiche un seul résultat
        $articles = $manager -> createQuery ('SELECT COUNT (a) FROM App\Entity\Article a')->getSingleScalarResult();
        $users = $manager -> createQuery('SELECT COUNT (u) FROM App\Entity\User u')->getSingleScalarResult();

        return $this -> render ('admin\dashboard\index.html.twig', [
            // compact permet de creer un tableau automatiquement en nommant des clés
            'stats' => compact('articles', 'users')
        ]);

    }

}
