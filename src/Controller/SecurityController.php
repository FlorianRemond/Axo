<?php

namespace App\Controller;

use App\Form\LogType;
use App\Form\RegistrationType;
use App\Form\ResettingType;
use App\Service\MailerService;
use Doctrine\Common\Persistence\ObjectManager;
use Swift_Mailer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
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
     * @param Swift_Mailer $mailer
     * @param AuthenticationUtils $utils
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function registration(Request $request, ObjectManager $manager,
                                 UserPasswordEncoderInterface $encoder,
                                 MailerService $mailerService, Swift_Mailer $mailer, AuthenticationUtils $utils)
    {
        //recupérer les erreurs d'authentification
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();
        $user = new User();
        $formUser = $this->createForm(RegistrationType::class, $user);

        //analyse de la requete passée
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {
            //envoi des données en base
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setToken($this->generateToken());
            $user->setCreatedAt(new\DateTime());
            $user->setConnectedAt(new \DateTime());
            $manager->persist($user);
            $manager->flush();

            //récupération des données
            $token = $user->getToken();
            $email = $user->getEmail();
            $username = $user->getUsername();
            //envoi de mail avec les données du User
            $mailerService->sendToken($token, $email, $username, 'registration.html.twig');
            //message informatif
            $this->addFlash(
                'success',
                'Merci de vous être enregistré, vous allez recevoir un email de validation !'
            );
            return $this->redirectToRoute('security_login');
        }
        //Vérification des données passées pour le User
        return $this->render('security/registration.html.twig', [
            'formUser' => $formUser->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    //formulaire de connexion

    /**
     * @Route("/connexion", name ="security_login")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        if (isset($_POST['submitpost'])) {
            if (isset($_POST ['g-recaptcha-response'])) {
                    $secret = '6Le9gbsUAAAAAO38NXkCckxDCooWToExnyAfZW0q';
                    $recaptcha = new \ReCaptcha\ReCaptcha($secret);
                    $resp = $recaptcha->setExpectedHostname(' recaptcha-demo.appspot.com ')
                        ->verify($_POST['g-recaptcha-response']);
                    if ($resp->isSuccess()) {
                        echo 'captcha ok ';
                    } else {
                        $errors = $resp->getErrorCodes();
                        echo 'captcha non ok ';
                        var_dump($errors);
                    }
                }else{
                var_dump('Captcha non rempli');
            }
        }
        //recupérer les erreurs d'authentification
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('security/login.html.twig', [

            //on passe a twig une variable qui récupère l'état de la variable $error
            'hasError' => $error !== null,

            //on récupère le nom d'utilisateur renseigné précédement et on le passe à twig
            'username' => $username,
        ]);
    }


    //Permet de se déconnecter

    /**
     * @Route ("/deconnexion", name="security_logout")
     */
    public function logout()
    {
        $this->addFlash('success', 'Vous êtes à présent déconnecté, à bientot !');
        // Route qui mène vers rien afin de sortir du site
    }
}
