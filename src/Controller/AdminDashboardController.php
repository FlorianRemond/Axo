<?php

namespace App\Controller;


use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function index(ObjectManager $manager)
    {
        $articles = $manager ->createQuery('SELECT a FROM App\Entity\Article a')->getResult();
        $users = $manager ->createQuery('SELECT u FROM App\Entity\User u')->getResult();

        dump($users);

        return $this->render('admin/dashboard/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
        ]);
    }
}
