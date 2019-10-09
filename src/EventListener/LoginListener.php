<?php


namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

/*Un eventlistener qui ici permet de mettre à jour la date de connexion lorsque le user se log */

class LoginListener
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onSecurityAuthenticationSuccess(AuthenticationEvent $event){
        $user = $event ->getAuthenticationToken()->getUser();

        if ($user instanceof User){
            $user ->setConnectedAt(new \DateTime());

            $this->em->persist($user);
            $this->em->flush();
        }
    }
}