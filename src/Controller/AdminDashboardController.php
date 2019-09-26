<?php

namespace App\Controller;


use App\Entity\User;
use App\Service\DateService;
use App\Service\MailerService;
use App\Service\VerificationService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\Expr\Select;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StatService;
use App\Repository\UserRepository;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     * @param ObjectManager $manager
     * @param DateService $dateService
     * @param MailerService $mailerService
     * @return Response
     * @throws Exception
     */
    public function index(ObjectManager $manager, DateService $dateService, MailerService $mailerService)
    {
        $dateService->getDate();

        //Vérification de la dernière date de connexion et envoi de mail si supérieur à 30 jours
        $user = $this->getUser();
        $society = $user->getSociety();
        $email = $user->getEmail();
        $username = $user->getUsername();
        $template = "verifByMail.html.twig";
        $connectedAt = $user->getConnectedAt();
        $connectedMonth = $connectedAt->modify('+30 day');
        $connectedOk = $connectedMonth->format('Y-m-d');
        $date = new \DateTime();
        $dateJour = $date->format('Y-m-d');
        if (($connectedOk < $dateJour) && ($society == ('Axocap'))) {
            $mailerService->sendToken(null, $email, $username, $template);
        }

        //ici on compte les articles en passant par une requete DQL et plutot que d'avoir les résultats sous forme de
        //tableau , la fonction ScalarResult affiche un seul résultat
        $articles = $manager->createQuery('SELECT COUNT (a) FROM App\Entity\Article a')->getSingleScalarResult();
        $users = $manager->createQuery('SELECT COUNT (u) FROM App\Entity\User u')->getSingleScalarResult();
        $datesConnexion = $manager->createQuery('SELECT u.connectedAt FROM App\Entity\User u')->getResult();

        //compteur de connexion quotidienne
        $count = 0;
        $dateN = new \DateTime();
        $dateNow = $dateN->format('Y-m-d');
        foreach ($datesConnexion as $dateConnexion) {
            $dateCo = $dateConnexion['connectedAt']->format('Y-m-d');
            if ($dateCo == $dateNow) {
                $count++;
            }
        }
        return $this->render('admin\dashboard\index.html.twig', [
            // compact permet de creer un tableau automatiquement en nommant des clés
            'stats' => compact('articles', 'users', 'count')
        ]);
    }

}
