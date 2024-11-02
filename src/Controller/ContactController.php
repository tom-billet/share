<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ContactRepository;

class ContactController extends AbstractController
{

    #[Route('/moderation/contacts', name: 'app_contacts')]
    public function ContactsList(ContactRepository $contactRepository): Response {
        $contacts = $contactRepository->findAll();
        return $this->render('contact/contacts.html.twig', [
            'contacts' => $contacts
        ]); 
    }
}
