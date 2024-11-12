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
        //On récupère tous les utilisateurs et les donne à la vue
        $users = $userRepository->findAll();
        return $this->render('user/users.html.twig', [
            'users'=>$users
        ]);
    }

    #[Route('/private/account', name: 'app_account')]
    public function profil(UserRepository $userRepository, Request $request, SubcategoryRepository $subcategoryRepository, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        
        //On prend l'utilisateur connecté
        $user = $this->getUser();
        //On prépare le nouveau File
        $file = new File();
        //On récupère toutes les subcategories triées par ordre alphabétique et par numéro
        $subcategories = $subcategoryRepository->findBy([], ['category'=>'asc', 'number'=>'asc']);
        //On passe les subcategories au formulaire
        $form = $this->createForm(FileUserType::class, $file, ['subcategories'=>$subcategories]);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){
                //On récupère la date
                $file->setSendingDate(new \Datetime());
                //On récupère les subcategories cochées
                $selectedSubcategories = $form->get('subcategories')->getData();
                foreach ($selectedSubcategories as $subcategory) {
                    //On ajoute chaque subcategory 
                    $file->addSubcategory($subcategory);
                }

                //On récupère le fichier
                $f = $form->get('file')->getData();
                if($f){
                    //On modifie le nom original du fichier
                    $fileNameServer = pathinfo($f->getClientOriginalName(),PATHINFO_FILENAME);
                    $fileNameServer = $slugger->slug($fileNameServer);
                    $fileNameServer = $fileNameServer.'-'.uniqid().'.'.$f->guessExtension();
                    //On récupère les informations du fichier et on les sauvegarde
                    try{
                        $file->setServerName($fileNameServer);
                        $file->setOriginalName($f->getClientOriginalName());
                        $file->setSendingDate(new \Datetime());
                        $file->setExtension($f->guessExtension());
                        $file->setSize($f->getSize());
                        $file->setUser($user);
                        $em->persist($file);
                        $em->flush();

                        $f->move($this->getParameter('file_directory'), $fileNameServer);
                        $this->addFlash('notice', 'Fichier envoyé');
                        return $this->redirectToRoute('app_account');
                    }
                    catch(FileException $e){
                        $this->addFlash('notice', 'Erreur d\'envoi');
                    }
                }
            }
        }

        //On passe les informations de l'utilisateur, le formulaire et les subcategories à la vue
        return $this->render('user/account.html.twig', [
            'user'=>$user,
            'form' => $form->createView(),
            'subcategories'=> $subcategories
        ]);
    }


    #[Route('/private/account-download/{id}', name: 'app_account_download',requirements: ["id"=>"\d+"] )]
    public function accountDownload(File $file) 
    {

        //Si l'id d'un fichier n'est pas passé dans l'URL
        if ($file == null){
            return $this->redirectToRoute('app_account');
        }
        else{
            //On regarde si l'utilisateur est bien le propriétaire du fichier
            if($file->getUser()!==$this->getUser()){
                $this->addFlash('notice', 'Vous n\'êtes pas le propriétaire de ce fichier');
                return $this->redirectToRoute('app_account');
            }
            
            //On retourne le fichier 
            //File_directory est modifiable dans services.yaml
            return $this->file($this->getParameter('file_directory').'/'.$file->getServerName(),
            //On récupère le nom original du fichier pour le téléchargement
            $file->getOriginalName());
        }
    }


    #[Route('/private/share/{id}', name: 'app_share')]
    public function share(File $file, Request $request, EntityManagerInterface $em): Response
    {

        //On récupère les friends (accepts) de l'utilisateur
        $friends = $this->getUser()->getAccept();

        //On les passe au formulaire
        $form = $this->createForm(SelectFriendsType::class, null, [
            'friends' => $friends
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //On récupère les amis sélectionnés et on leur partage le fichier
            $selectedFriends = $form->get('friends')->getData();
            foreach ($selectedFriends as $friend) {
                $file->addShare($friend);
            }
            $em->flush();
            $this->addFlash('notice','Fichier partagé');
            return $this->redirectToRoute('app_account');
        }

        //On passe les données du fichier et le formulaire à la vue
        return $this->render('user/share.html.twig', [
            'file'=>$file,
            'form' => $form->createView(),
        ]);
    }
}
