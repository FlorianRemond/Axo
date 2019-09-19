<?php


namespace App\Service;



//Vérification de la date de dernière co des admin et envoi de mail si + de 30jours
use App\Entity\User;
use App\Repository\UserRepository;

class VerificationService
{
    public function verifDateAdmin(User $user){
              $user->getSociety();
        dump($user);

    }
}