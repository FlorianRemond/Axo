<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     * @param AuthenticationUtils $utils
     * @param UserRepository $userRepository
     * @return Response
     */

    /* Ici on crée un controller afin de bien différencier le login admin et le login du front */
    public function login(AuthenticationUtils $utils, UserRepository $userRepository)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();


        return $this->render('admin/account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username ]);
    }
}