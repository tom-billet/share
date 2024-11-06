<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\UserRepository;
use App\Repository\SubcategoryRepository;
use App\Entity\File;
use App\Entity\User;
use App\Form\FileUserType;
use App\Form\AddFriendType;
use App\Form\SelectFriendsType;

class UserController extends AbstractController
{
    #[Route('/admin/users', name: 'app_users')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/users.html.twig', [
            'users'=>$users
        ]);
    }

    #[Route('/private/account', name: 'app_account')]
    public function profil(UserRepository $userRepository, Request $request, SubcategoryRepository $subcategoryRepository, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        
        $user = $this->getUser();

        $file = new File();
        $subcategories = $subcategoryRepository->findBy([], ['category'=>'asc', 'number'=>'asc']);
        $form = $this->createForm(FileUserType::class, $file, ['subcategories'=>$subcategories]);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){
                $file->setSendingDate(new \Datetime());
                $selectedSubcategories = $form->get('subcategories')->getData();
                foreach ($selectedSubcategories as $subcategory) {
                    $file->addSubcategory($subcategory);
                }

                $f = $form->get('file')->getData();
                if($f){
                    $nomFichierServeur = pathinfo($f->getClientOriginalName(),PATHINFO_FILENAME);
                    $nomFichierServeur = $slugger->slug($nomFichierServeur);
                    $nomFichierServeur = $nomFichierServeur.'-'.uniqid().'.'.$f->guessExtension();
                    try{
                        $file->setServerName($nomFichierServeur);
                        $file->setOriginalName($f->getClientOriginalName());
                        $file->setSendingDate(new \Datetime());
                        $file->setExtension($f->guessExtension());
                        $file->setSize($f->getSize());
                        $file->setUser($user);
                        $em->persist($file);
                        $em->flush();

                        $f->move($this->getParameter('file_directory'), $nomFichierServeur);
                        $this->addFlash('notice', 'Fichier envoyé');
                        return $this->redirectToRoute('app_account');
                    }
                    catch(FileException $e){
                        $this->addFlash('notice', 'Erreur d\'envoi');
                    }
                }
            }
        }


        return $this->render('user/account.html.twig', [
            'user'=>$user,
            'form' => $form->createView(),
            'subcategories'=> $subcategories
        ]);
    }


    #[Route('/private/account-download/{id}', name: 'app_account_download',requirements: ["id"=>"\d+"] )]
    public function accountDownload(File $file) 
    {

        if ($file == null){
            return $this->redirectToRoute('app_account');
        }
        else{
            if($file->getUser()!==$this->getUser()){
                $this->addFlash('notice', 'Vous n\'êtes pas le propriétaire de ce fichier');
                return $this->redirectToRoute('app_account');
            }
            return $this->file($this->getParameter('file_directory').'/'.$file->getServerName(),
            $file->getOriginalName());
        }
    }


    #[Route('/private/share/{id}', name: 'app_share')]
    public function share(File $file, Request $request, EntityManagerInterface $em): Response
    {

        $friends = $this->getUser()->getAccept();

        $form = $this->createForm(SelectFriendsType::class, null, [
            'friends' => $friends
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $selectedFriends = $form->get('friends')->getData();
            foreach ($selectedFriends as $friend) {
                $file->addShare($friend);
            }
            $em->flush();
            $this->addFlash('notice','Fichier partagé');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('user/share.html.twig', [
            'file'=>$file,
            'form' => $form->createView(),
        ]);
    }
}
