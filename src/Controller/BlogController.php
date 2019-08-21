<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    //route principale sans adresse
    /**
     * @Route("/",name="home")
     * @Route("/blog",name="blog")
     */
    public function home(ArticleRepository $repo){
        $articles = $repo -> findAll();
        return $this -> render('blog/home.html.twig', [
            'controller_name' => 'BlogController',
            'articles'=>$articles]);
    }

    //Utilisation de routes multiples afin d'utiliser une seule fois le formulaire
    /**
     * @Route ("/blog/new",name="blog_create")
     * @Route ("/blog/{id}/edit",name="blog_edit")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     */
    public function create (Article $article = null, Request $request, ObjectManager $manager){

        if(!$article) {
            $article = new Article();
        }
        //création du formulaire basé sur l'entité Article
        // on peut utiliser le fomulaire ArticleType ou la solution ci-dessous
       // $form = $this ->createFormBuilder($article)
       //          ->add('title', TextType::class)
       //         ->add ('content', TextareaType::class)
       //         ->add ('image')
       //         -> getForm();

        //ici on appelle le formulaire et on le lie à l'article
        $form = $this -> createForm(ArticleType::class,$article);

        //analyse de la requete Http que je passe en paramètre
        $form ->handleRequest($request);

      //vérifier les données passées dans l'article
      //dump($article);

      //Vérification de la soumission du formulaire et de la confirmité de celui-ci
      if($form->isSubmitted() && $form ->isValid()){
          if(!$article->getId()) {
              //ajout de la date de la création des l'ajout de l'article
              $article->setCreatedAt(new \DateTime());
          }
          //on fait persister l'article dans le temps
          $manager->persist($article);
          //on envoi l'article en BDD
          $manager->flush();
          //ici on repart vers la vue de l'article enregistré précédement,
          //en récupérant l'id de celui-ci et en l'injectant dans la route
          return $this ->redirectToRoute('blog_show',['id'=>$article->getId()]);
      }
        return $this -> render ('blog/create.html.twig',[
            'formArticle'=> $form->createView(),
            //changer le texte du bouton en fonction de si l'on édite ou si l'on crée un article
            'editMode' => $article->getId()!== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_usershow")
     * @Security("is_granted('ROLE_USER')")
     * @param Article $article
     * @return Response
     */

    public function show (Article $article)
    {
        //$repo = $this -> getDoctrine()-> getRepository(Article::class);
        // $article =$repo -> find($id);

        return $this->render('blog/usershow.html.twig', [
            'article' => $article,
        ]);
    }
}
