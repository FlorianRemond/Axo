<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminMailController extends AbstractController
{
    /**
     * @Route("/admin/mail", name="admin_mail")
     */

    public function index ( \Swift_Mailer $mailer, User $user){

        $listmail=$user->getEmail();
        dump($listmail);

        $message=(new \Swift_Message('Message de test en provenance d\'Axocap'))
            ->setFrom('axocapmailing@gmail.com')
            ->setTo('axocapmailing@gmail.com')
            ->setBody('Un nouvel article d\'Axocap est paru, il n\'attend que votre lecture ! Signé : le dev Stagiaire' );
        $mailer -> send($message);
        dump($message);
        return $this ->render('admin/mail/mail.html.twig');
    }
}
