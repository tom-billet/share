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
use App\Form\FileUserType;

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
}
