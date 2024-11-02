<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

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
    public function profil(UserRepository $userRepository): Response
    {
        
        $user = $this->getUser();

        return $this->render('user/account.html.twig', [
            'user'=>$user
        ]);
    }
}
