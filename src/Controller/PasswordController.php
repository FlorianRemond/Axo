<?php

namespace App\Controller;
use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\PasswordUpdateType;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swift_Mailer;
use App\Form\MailResetType;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function Sodium\add;

class PasswordController extends AbstractController
{

    /**
     * @return string
     * @throws Exception
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * @Route ("/mail-password-reset", name="mail_password_reset")
     * @param Request $request
     * @param MailerService $mailerService
     * @param Swift_Mailer $mailer
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function sendMailPasswordReset(Request$request, MailerService $mailerService,
                                          \Swift_Mailer $mailer)
    {
        $manager = $this->getDoctrine()->getManager();
        $formMailReset = $this->createForm(MailResetType::class);
        $formMailReset->handleRequest($request);
        $email= $formMailReset-> get('email')->getData();

        if ($formMailReset->isSubmitted() && $formMailReset->isValid()) {
            $user = $manager->getRepository(User::class)->findOneBy(['email' => $email]);
            $token = random_bytes(16);
//Conversion du binaire en hexadécimal
            $token = bin2hex($token);
            $user->setToken($token);
            $manager->persist($user);
            $manager->flush();

            if ($user === null) {
                $this->addFlash('user-error',
                    'Utilisateur inconnu, merci de renseigner un email valide');
                return $this->redirectToRoute('security_registration');
            }
            $token = $user->getToken();
            $email = $user->getEmail();
            $template = 'MailtoResetPassword.html.twig';
            $username = $user->getUsername();
            $mailerService->sendToken($token, $email, $username, $template);
            return $this->redirectToRoute('home');
        }
        return $this->render('security/mailPasswordReset.html.twig',['formMailReset' => $formMailReset->createView()
        ]);
    }

    /**
     * modification du mot de passe après réception du mail
     * @Route ("/password-reset",name="security_password_reset"
     */
    public function passwordReset(){
        
    }

    /**
     * Permet de modifier le mot de passe
     * @Route("/password-update",name="security_password_update")
     * @Security("is_granted('ROLE_USER')")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param ObjectManager $manager
     * @param MailerService $mailerService
     * @param Swift_Mailer $mailer
     * @return Response
     */
    public function UpdatePassword (Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager,
                                    MailerService $mailerService, Swift_Mailer $mailer)
    {
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
                $email =$user->getEmail();
                $username = $user ->getUsername();
                $template='confirmPasswordChange.html.twig';
                $mailerService ->sendToken($mailer,$email, $username,$template);
                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifié! Un email de confirmation vient de vous être envoyé.'
                );
                return $this->redirectToRoute('security_login');
            }
        }
        return $this->render('security/password.html.twig',[
            'formPassword'=> $formPassword->createView()
        ]);
    }




}
