<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FileType;
use App\Entity\File;
use App\Entity\User;
use App\Repository\FileRepository;
use App\Repository\SubcategoryRepository;
use App\Repository\UserRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileController extends AbstractController
{
    #[Route('/admin/add-file', name: 'app_add_file')]
    public function add_file(Request $request, SubcategoryRepository $subcategoryRepository, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $file = new File();
        $subcategories = $subcategoryRepository->findBy([], ['category'=>'asc', 'number'=>'asc']);
        $form = $this->createForm(FileType::class, $file, ['subcategories'=>$subcategories]);

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
                        $em->persist($file);
                        $em->flush();

                        $f->move($this->getParameter('file_directory'), $nomFichierServeur);
                        $this->addFlash('notice', 'Fichier envoyé');
                        return $this->redirectToRoute('app_add_file');
                    }
                    catch(FileException $e){
                        $this->addFlash('notice', 'Erreur d\'envoi');
                    }
                }
            }
        }

        return $this->render('file/add_file.html.twig', [
            'form' => $form->createView(),
            'subcategories'=> $subcategories
        ]);
    }


    #[Route('/admin/files', name: 'app_files')]
    public function Files(FileRepository $fileRepository): Response {
        $files = $fileRepository->findAll();
        return $this->render('file/files.html.twig', [
            'files' => $files
        ]); 
    }


    #[Route('/private/files-user', name: 'app_files_by_user')]
    public function filesByUser(UserRepository $userRepository): Response{

        $users = $userRepository->findBy([], ['surname'=>'asc', 'name'=>'asc']);

        return $this->render('file/files_by_user.html.twig', [
            'users'=>$users
        ]);
    }


    #[Route('/private/download/{id}', name: 'app_download', requirements:["id"=>"\d+"] )]
    public function download(File $file) 
    {

        if ($file == null){
            $this->redirectToRoute('app_files_by_user'); }
        else{
            return $this->file($this->getParameter('file_directory').'/'.$file->getServerName(),
            $file->getOriginalName());
        }
    }

    #[Route('/private/share-download/{id}', name: 'app_share_download',requirements: ["id"=>"\d+"] )]
    public function shareDownload(File $file) 
    {

        $user = $this->getUser();
        if ($file == null){
            return $this->redirectToRoute('app_account');
        }
        else{
            if(!$file->getShare()->contains($user)){
                $this->addFlash('notice', 'Ce fichier ne vous a pas été partagé');
                return $this->redirectToRoute('app_shared_files');
            }
            return $this->file($this->getParameter('file_directory').'/'.$file->getServerName(),
            $file->getOriginalName());
        }
    }

    #[Route('/private/shared-files', name: 'app_shared_files')]
    public function sharedFiles(): Response {

        $user = $this->getUser();
        $sharedFiles = $this->getUser()->getFileShare($user);

        return $this->render('file/shared_files.html.twig', [
            'sharedFiles' => $sharedFiles
        ]); 
    }
}
