<?php

namespace App\Controller;


use App\Service\StatsService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, StatsService $statsService)
    {

        $stats= $statsService->getStats();

           return $this->render('admin/dashboard/index.html.twig', [

           //compact creee un tableau avec les meme clé que ci dessus
            'stats' =>$stats
        ]);


    }
}
