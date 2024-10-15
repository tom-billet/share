<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ContactRepository;

class ContactController extends AbstractController
{

    #[Route('/moderation/contacts-list', name: 'app_contacts_lists')]
    public function ContactsList(ContactRepository $contactRepository): Response {
        $contacts = $contactRepository->findAll();
        return $this->render('contact/contacts_list.html.twig', [
            'contacts' => $contacts
        ]); 
    }
}
