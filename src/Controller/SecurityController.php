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
    //formulaire d'inscription
    /**
     * @Route("/inscription", name="security_registration")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param MailerService $mailerService
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function registration(Request $request, ObjectManager $manager,
                                 UserPasswordEncoderInterface $encoder,MailerService $mailerService,
                                 AuthenticationUtils $authenticationUtils){

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = new User();
        $formUser=$this->createForm(RegistrationType::class, $user);

        //analyse de la requete passée
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()){
            $hash =$encoder ->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $user->setCreatedAt(new\DateTime());
            $user->setConnectedAt(new \DateTime());
            $manager->persist($user );
            $manager->flush();
            $token = $user->getToken();
            $email = $user->getEmail();
            $username = $user->getUsername();
            $mailerService->sendToken( $token, $email, $username, 'registration.html.twig');
            $this->addFlash('user-error', 'Votre inscription a été validée, vous aller recevoir un email de confirmation pour activer votre compte et pouvoir vous connecté');

           return $this -> redirectToRoute('security_login');
        }
        //Vérification des données passées pour le User
        return $this-> render('security/registration.html.twig',[
        'formUser' => $formUser->createView(),'last_username' => $lastUsername, 'error' => $error,
        ]);
    }

    /**
     * @Route("/account/confirm/{token}/{username}", name="confirm_account")
     * @param $token
     * @param $username
     * @return Response
     */
    public function confirmAccount($token, $username): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        $tokenExist = $user->getConfirmationToken();
        if($token === $tokenExist) {
            $user->setConfirmationToken(null);
            $user->setEnabled(true);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('security_login');
        }else{
            return $this->render('registration/token-expire.html.twig');
        }
    }

    /**
     * @Route("/send-token-confirmation", name="send_confirmation_token")
     * @param Request $request
     * @param MailerService $mailerService
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function sendConfirmationToken(Request $request, MailerService $mailerService, \Swift_Mailer $mailer): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $email = $request->request->get('email');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
        if($user === null) {
            $this->addFlash('not-user-exist', 'utilisateur non trouvé');
            return $this->redirectToRoute('security_registration');
        }
        $user->setConfirmationToken($this->generateToken());
        $em->persist($user);
        $em->flush();
        $token = $user->getConfirmationToken();
        $email = $user->getEmail();
        $username = $user->getUsername();
        $mailerService->sendToken( $token, $email, $username, 'registration.html.twig');
        return $this->redirectToRoute('security_login');
    }

    /**
     * @Route("/mot-de-passe-oublier", name="forgotten_password")
     * @param Request $request
     * @param MailerService $mailerService
     * @param \Swift_Mailer $mailer
     * @return Response
     * @throws \Exception
     */

    public function forgottenPassword(Request $request, MailerService $mailerService, \Swift_Mailer $mailer): Response
    {
        if($request->isMethod('POST')) {
            $email = $request->get('email');
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
            if($user === null) {
                $this->addFlash('user-error', 'Utilisateur non trouvé');
                return $this->redirectToRoute('security_registration');
            }
            $user->setTokenPassword($this->generateToken());
            $user->setCreatedTokenPasswordAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $token = $user->getTokenPassword();
            $email = $user->getEmail();
            $username = $user->getUsername();
            $mailerService->sendToken( $token, $email, $username, 'forgotten_password.html.twig');
            return $this->redirectToRoute('home');
        }
        return $this->render('#');
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

    /**
     * @return string
     * @throws \Exception
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
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
