<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */

    /* Ici on crée un controller afin de bien différencier le login admin et le login du front */
    public function login()
    {
        return $this->render('admin/account/login.html.twig', [

        ]);
    }
}
