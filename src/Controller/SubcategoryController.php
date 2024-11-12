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
        /*Préparer la création de la subcategory*/
        $subcategory = new Subcategory();
        $form = $this->createForm(AddSubcategoryType::class,$subcategory);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                try {
                    //S'il n'y a pas d'erreur, on sauvegarde l'ajout
                    $em->persist($subcategory);
                    $em->flush();
                } catch (\RuntimeException $e) {
                    //Sinon on affiche l'erreur et on recharge la page
                    $this->addFlash('notice', $e->getMessage());
                    return $this->redirectToRoute('app_add_subcategory');
                }
                $this->addFlash('notice','Sous-catégorie ajoutée');
                return $this->redirectToRoute('app_add_subcategory');
            }
        }

        //On envoie le formulaire à la vue
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
                //On sauvegarde la modification et on retourne sur la page categories
                $em->persist($subcategory);
                $em->flush();
                $this->addFlash('notice','Sous-catégorie modifiée');
                return $this->redirectToRoute('app_categories');
            }
        }
        
        //On envoie le formulaire à la vue
        return $this->render('subcategory/edit_subcategory.html.twig', [
            'form' => $form->createView()
        ]); 
    }


    #[Route('/moderation/delete-subcategory/{id}', name: 'app_delete_subcategory')]
    public function deleteSubcategory(Request $request, Subcategory $subcategory, EntityManagerInterface $em): Response {

        /*Si un id de subcategory est passé dans l'URL, on supprime la subcategory et on recharge la page*/
        if($subcategory!=null){
            $em->remove($subcategory);
            $em->flush();
            $this->addFlash('notice','Sous-catégorie supprimée');
        }

        return $this->redirectToRoute('app_categories');
    }
}
