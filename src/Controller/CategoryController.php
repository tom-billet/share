<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
{

    #[Route('/categories-list', name: 'app_categories_lists')]
    public function CategoriesList(CategoryRepository $categoryRepository): Response {
        $categories = $categoryRepository->findAll();
        return $this->render('category/categories_list.html.twig', [
            'categories' => $categories
        ]); 
    }
}
