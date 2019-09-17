<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\EditType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_user_index")
     * @Route("is_granted('ROLE_ADMIN')")
     *@
     * @param UserRepository $repo
     * @return Response
     */
    public function index(UserRepository $repo){

        return $this->render('admin/user/index.html.twig', [
          'users'=> $repo ->findAll()
        ]);
    }

    /**
     * Suppression des users en BDD
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     * @param User $user
     * @Route("is_granted('ROLE_ADMIN')")
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(User $user, ObjectManager $manager){
        $manager->remove($user);
        $manager->flush();
        $this ->addFlash('success','L\'utilisateur a bien été supprimé');
        return $this ->redirectToRoute('admin_user_index');
    }

    /**
     * @Route("/admin/user/create", name="admin_user_create")
     * @Route("is_granted('ROLE_ADMIN')")
     * @param User|null $user
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function create (User $user=null, Request $request, ObjectManager $manager,
                            UserPasswordEncoderInterface $encoder){

        if(!$user){
            $user=new User();
        }
        $formUser=$this->createForm(RegistrationType::class, $user);
        //analyse de la requete passée
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()){
            if (!$user->getId()) {
                $user->setCreatedAt(new\DateTime());
                $user->setConnectedAt(new\DateTime());

            }
            $hash =$encoder ->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user );
            $manager->flush();


            return $this -> redirectToRoute('admin_user_index');
        }
        //Vérification des données passées pour le User
        //dump($user);
        return $this-> render('security/registration.html.twig',[
            'formUser' => $formUser->createView()
        ]);
    }


    /**
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     * @Route("is_granted('ROLE_ADMIN')")
     * @param User $user
     * @param Request $request
     * @param ObjectManager $manager
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function edit(User $user,Request $request,ObjectManager $manager){

        $formEditUser=$this->createForm(EditType::class, $user);
        //analyse de la requete passée
        $formEditUser->handleRequest($request);

        if ($formEditUser->isSubmitted() && $formEditUser->isValid()){

            $manager->persist($user );
            $manager->flush();

            return $this -> redirectToRoute('admin_user_index');
        }
        //Vérification des données passées pour le User
        //dump($user);
        return $this-> render('admin/user/edit.html.twig',[
            'formUser' => $formEditUser->createView()
        ]);
    }


}
