<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Form\EditCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{

    #[Route('/moderation/categories', name: 'app_categories')]
    public function Categories(CategoryRepository $categoryRepository): Response {

        $categories = $categoryRepository->findAll();

        return $this->render('category/categories.html.twig', [
            'categories' => $categories
        ]); 
    }


    #[Route('/moderation/edit-category/{id}', name: 'app_edit_category')]
    public function EditCategory(Request $request, Category $category, EntityManagerInterface $em): Response {

        $form = $this->createForm(EditCategoryType::class, $category);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $em->persist($category);
                $em->flush();
                $this->addFlash('notice','Catégorie modifiée');
                return $this->redirectToRoute('app_categories');
            }
        }
        
        return $this->render('category/edit_category.html.twig', [
            'form' => $form->createView()
        ]); 
    }
}
