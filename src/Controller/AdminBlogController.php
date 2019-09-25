<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminBlogController extends AbstractController
{

    /**
     * @Route("/admin/articles", name="admin_article_index")
     * @Route("is_granted('ROLE_ADMIN')")
     * @param ArticleRepository $repo
     * @return Response
     */
    public function index(ArticleRepository $repo)
    {
        return $this->render('admin/blog/index.html.twig', [
            'articles' => $repo->findAll()
        ]);
    }

    /**
     * @Route("/admin/create", name="admin_article_create")
     * @Route("/admin/{id}/edit", name="admin_article_edit")
     * @Route("is_granted('ROLE_ADMIN')")
     * @param Article|null $article
     * @param Request $request
     * @param ObjectManager $manager
     * @return RedirectResponse|Response
     * @throws Exception
     */

    public function create(Article $article = null, Request $request, ObjectManager $manager)
    {
        if (!$article) {
            $article = new Article ();
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }
            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success', 'L\' article a bien été crée');
            return $this->redirectToRoute('admin_mail');
        }
        return $this->render('admin/blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * Suppression des articles en BDD
     * @Route("/admin/{id}/delete", name="admin_article_delete")
     * @param Article $article
     * @Route("is_granted('ROLE_ADMIN')")
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Article $article, ObjectManager $manager)
    {
        $manager->remove($article);
        $manager->flush();
        $this->addFlash('success', 'L\'article a bien été supprimé');
        return $this->redirectToRoute('admin_article_index');

    }
}
