<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{


    //formulaire d'inscription
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
            $this->addFlash(
                'success',
                'Merci de vous être enregistré !'
            );

            return $this -> redirectToRoute('security_login');
        }
        //Vérification des données passées pour le User
        //dump($user);
        return $this-> render('security/registration.html.twig',[
        'formUser' => $formUser->createView()
        ]);
    }



    //formulaire de connexion
    /**
     * @Route("/connexion", name ="security_login")
     */
    public function login(AuthenticationUtils $utils){

        //recupérer les erreurs d'authentification
        $error = $utils->getLastAuthenticationError();
        $username = $utils -> getLastUsername();

        //dump($error);
        return $this -> render('security/login.html.twig',[
            //on passe a twig une variable qui récupère l'état de la variable $error
            'hasError' => $error !==null,
           //on récupère le nom d'utilisateur renseigné précédement et on le passe à twig
            'username'=> $username
            ]);
    }

    //Permet de se déconnecter
    /**
     * @Route ("/deconnexion", name="security_logout")
     *
     */
    public function logout (){
        // Route qui mène vers rien afin de sortir du site
    }




    //formulaire de modification du profil
    /**
     * @Route("/profile", name="security_profile")
     *
     *
     */
    public function profile(Request $request, ObjectManager $manager){
        $user = $this->getUser();

        $formProfile = $this->createForm(AccountType::class,$user);

        $formProfile->handleRequest($request);
        if($formProfile ->isSubmitted() && $formProfile->isValid()){

            $manager ->persist($user);
            $manager ->flush();

            $this->addFlash(
                'sucess',
                'Le profil a été modifié avec succès ! '
            );
        }

        return $this ->render('security/profile.html.twig',[
            'formProfile' => $formProfile->createView(),
        ]);
    }


    /**
     * Permet de modifier le mot de passe
     * @Route("/password-update",name="security_password_update")
     * @Security("is_granted('ROLE_USER')")
     * @return Response
     */

        public function UpdatePassword (Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager){
            $passwordUpdate = new PasswordUpdate();

            $user = $this ->getUser();

            $formPassword =$this ->createForm(PasswordUpdateType::class,$passwordUpdate);

            $formPassword->handleRequest($request);

            if($formPassword->isSubmitted() && $formPassword->isValid()){
                // Vérification de l'ancien mot de passe par rapport à la base
               if (!password_verify($passwordUpdate->getOldPassword(), $user -> getPassword())){
                // Gestion de l'erreur
                    $formPassword->get('oldPassword')-> addError(new FormError("Le mot de passe fourni n'est 
                    pas le bon mot de passe"));
                }else{
                    $newPassword = $passwordUpdate->getNewPassword();
                    $hash=$encoder->encodePassword($user, $newPassword);

                    $user->setPassword($hash);

                    $manager->persist($user);
                    $manager->flush();

                    $this->addFlash(
                        'success',
                        'Votre mot de passe a bien été modifié !'
                    );

                    return $this->redirectToRoute('home');
                }

            }


            return $this->render('security/password.html.twig',[
            'formPassword'=> $formPassword->createView()
                ]);
        }





}
