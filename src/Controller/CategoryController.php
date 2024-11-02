<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
{

    #[Route('/moderation/categories', name: 'app_categories')]
    public function Categories(CategoryRepository $categoryRepository): Response {
        $categories = $categoryRepository->findAll();
        return $this->render('category/categories.html.twig', [
            'categories' => $categories
        ]); 
    }
}
