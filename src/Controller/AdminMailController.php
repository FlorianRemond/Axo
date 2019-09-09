<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminMailController extends AbstractController
{

    /**
     * @Route("/admin/mail", name="admin_mail")
     */

    public function indexMail ( \Swift_Mailer $mailer, UserRepository $userRepo)
    {

       /* Ici on fait appel au userRepo pour récupérer les mails en base afin de les passer ensuite à setTo sous la forme
       attendu pour une mailing list */
        $users = $userRepo->findMailUsers();
        $destinataires = [];
        foreach ($users as $user) {
            $destinataires[] = $user['email'];
            dump($destinataires);
        }
        $message = (new \Swift_Message('Message informatif en provenance d\'Axocap'))
            ->setFrom('AxocapMailing@axocap.com')
            ->setTo($destinataires)
            ->setBody('Un nouvel article d\'Axocap est paru, il n\'attend que vôtre lecture ! ');
        $mailer->send($message);
        return $this->redirectToRoute('admin_article_index');

    }
}
