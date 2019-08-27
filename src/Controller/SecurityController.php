<?php

namespace App\Controller;

use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, ObjectManager $manager,
                                 UserPasswordEncoderInterface $encoder){
        $user = new User();
        $formUser=$this->createForm(RegistrationType::class, $user);

        //analyse de la requete passée
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()){
            $hash =$encoder ->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user );
            $manager->flush();

            return $this -> redirectToRoute('security_login');
        }
        //Vérification des données passées pour le User
        //dump($user);
        return $this-> render('security/registration.html.twig',[
        'formUser' => $formUser->createView()
        ]);
    }

    /**
     * @Route("/connexion", name ="security_login")
     */
    public function login(){

        return $this -> render('security/login.html.twig');
    }

    /**
     * @Route ("/deconnexion", name="security_logout")
     *
     */
    public function logout (){

    }
}
