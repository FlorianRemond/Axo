<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Form\AccountType;
use App\Form\LogType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    /**
     * @return string
     * @throws \Exception
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }




    //formulaire d'inscription

    /**
     * @Route("/inscription", name="security_registration")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param MailerService $mailerService
     * @param \Swift_Mailer $mailer
     * @param AuthenticationUtils $utils
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function registration(Request $request, ObjectManager $manager,
                                 UserPasswordEncoderInterface $encoder,
                                 MailerService $mailerService, \Swift_Mailer $mailer,AuthenticationUtils $utils){
        //recupérer les erreurs d'authentification
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils -> getLastUsername();
        $user = new User();
        $formUser=$this->createForm(RegistrationType::class, $user);

        //analyse de la requete passée
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()){
            //envoi des données en base
            $hash =$encoder ->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $user->setToken($this->generateToken());
            $user->setCreatedAt(new\DateTime());
            $user->setConnectedAt(new \DateTime());
            $manager->persist($user );
            $manager->flush();

            //récupération des données
            $token = $user->getToken();
            $email = $user ->getEmail();
            $username= $user ->getUsername();
            //envoi de mail avec les données du User
            $mailerService ->sendToken($mailer,$token,$email,$username,'registration.html.twig');

            $this->addFlash(
                'success',
                'Merci de vous être enregistré, vous allez recevoir un email de validation !'
            );
            return $this -> redirectToRoute('security_login');
        }
        //Vérification des données passées pour le User
        return $this-> render('security/registration.html.twig',[
            'formUser' => $formUser->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }



    //formulaire de connexion
    /**
     * @Route("/connexion", name ="security_login")
     *
     */
    public function login(AuthenticationUtils $utils){
        //recupérer les erreurs d'authentification
        $error = $utils->getLastAuthenticationError();
        $username = $utils -> getLastUsername();

        return $this -> render('security/login.html.twig',[
            //on passe a twig une variable qui récupère l'état de la variable $error
            'hasError' => $error !==null,
            //on récupère le nom d'utilisateur renseigné précédement et on le passe à twig
            'username'=> $username,
        ]);
    }

    //Permet de se déconnecter
    /**
     * @Route ("/deconnexion", name="security_logout")
     */
    public function logout (){
        // Route qui mène vers rien afin de sortir du site
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
