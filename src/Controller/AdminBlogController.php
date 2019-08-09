<?php

namespace App\Controller;



use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminBlogController extends AbstractController{

    /**
     * @Route("/admin/articles", name="admin_article_index")
     */
    public function index(ArticleRepository $repo)
    {
        return $this->render('admin/blog/index.html.twig', [
            'articles' => $repo->findAll()
        ]);
    }
}
