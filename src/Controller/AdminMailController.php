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

    public function index ( \Swift_Mailer $mailer, UserRepository $userRepo)
    {

     /*
        $listMail = $userRepo->findMailUsers();
        //dump($listmail);
        foreach ($listMail as $email) {
            //  dump($mail);
            foreach ($email as $mailingList){
                dump( $mailingList) ;
                //dump($mailingList);
                $message = (new \Swift_Message('Message de test en provenance d\'Axocap'))
                  //  ->setFrom('axocapmailing@gmail.com')
                    ->setFrom ('test@gmail.com')
                    ->setTo($mailingList)
                    // ->setTo()
                    ->setBody('Un nouvel article test est paru, il n\'attend que votre lecture ! ');
                $mailer->send($message);
                dump($message);
            }

        }
        return $this->render('admin/mail/mail.html.twig');


     */
    /* Ici on fait appel au userRepo pour récupérer les mails en base afin de les passer ensuite à setTo sous la forme
    attendu pour une mailing list */
        $users = $userRepo->findMailUsers();
        $destinataires = [];
        foreach ($users as $user) {
            $destinataires[] = $user['email'];
            dump($destinataires);
        }

        $message = (new \Swift_Message('Message informatif en provenance d\'Axocap'))
            ->setFrom('test@gmaili.com')
            ->setTo($destinataires)
            ->setBody('Un nouvel article d\'Axocap est paru, il n\'attend que vôtre lecture ! ');
        $mailer->send($message);
        return $this->render('admin/mail/mail.html.twig');

        }



}
