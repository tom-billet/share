<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FileType;
use App\Entity\File;
use App\Repository\FileRepository;
use App\Repository\SubcategoryRepository;
use App\Repository\UserRepository;

class FileController extends AbstractController
{
    #[Route('/admin/add-file', name: 'app_add_file')]
    public function add_file(Request $request, SubcategoryRepository $subcategoryRepository, EntityManagerInterface $em): Response
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

                $em->persist($file);
                $em->flush();
                $this->addFlash('notice','File added');
                return $this->redirectToRoute('app_add_file');
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


    #[Route('/admin/files-user', name: 'app_files_by_user')]
    public function filesByUser(UserRepository $userRepository): Response{

        $users = $userRepository->findBy([], ['surname'=>'asc', 'name'=>'asc']);

        return $this->render('file/files_by_user.html.twig', [
            'users'=>$users
        ]);
    }
}
