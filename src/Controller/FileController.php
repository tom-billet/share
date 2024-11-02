<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FileType;
use App\Entity\File;

class FileController extends AbstractController
{
    #[Route('/admin/add-file', name: 'app_add_file')]
    public function add_file(Request $request, EntityManagerInterface $em): Response
    {
        $file = new File();
        $form = $this->createForm(FileType::class, $file);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){
                $file->setSendingDate(new \Datetime());
                $em->persist($file);
                $em->flush();
                $this->addFlash('notice','File added');
                return $this->redirectToRoute('app_add_file');
            }
        }

        return $this->render('file/add_file.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
