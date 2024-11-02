<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\AddSubcategoryType;
use App\Entity\Subcategory;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EditSubcategoryType;

class SubcategoryController extends AbstractController
{
    #[Route('/moderation/add-subcategory', name: 'app_add_subcategory')]
    public function add_Subcategory(Request $request, EntityManagerInterface $em): Response
    {
        $subcategory = new Subcategory();
        $form = $this->createForm(AddSubcategoryType::class,$subcategory);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                try {
                    $em->persist($subcategory);
                    $em->flush();
                } catch (\RuntimeException $e) {
                    $this->addFlash('notice', $e->getMessage());
                    return $this->redirectToRoute('app_add_subcategory');
                }
                $this->addFlash('notice','Sous-catégorie ajoutée');
                return $this->redirectToRoute('app_add_subcategory');
            }
        }

        return $this->render('subcategory/add_subcategory.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/moderation/edit-subcategory/{id}', name: 'app_edit_subcategory')]
    public function editSubcategory(Request $request, Subcategory $subcategory, EntityManagerInterface $em): Response {

        $form = $this->createForm(EditSubcategoryType::class, $subcategory);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $em->persist($subcategory);
                $em->flush();
                $this->addFlash('notice','Sous-catégorie modifiée');
                return $this->redirectToRoute('app_categories');
            }
        }
        
        return $this->render('subcategory/edit_subcategory.html.twig', [
            'form' => $form->createView()
        ]); 
    }


    #[Route('/moderation/delete-subcategory/{id}', name: 'app_delete_subcategory')]
    public function deleteSubcategory(Request $request, Subcategory $subcategory, EntityManagerInterface $em): Response {

        if($subcategory!=null){
            $em->remove($subcategory);
            $em->flush();
            $this->addFlash('notice','Sous-catégorie supprimée');
        }

        return $this->redirectToRoute('app_categories');
    }
}
