<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ContactType;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contact;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class BaseController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('base/index.html.twig', [

        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('base/about.html.twig', [

        ]);
    }

    #[Route('/legal', name: 'app_legal')]
    public function legal(): Response
    {
        return $this->render('base/legal.html.twig', [

        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contactForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Sanitize inputs
            $contact->setName(filter_var($contact->getName(), FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $contact->setEmail(filter_var($contact->getEmail(), FILTER_SANITIZE_EMAIL));
            $contact->setTopic(filter_var($contact->getTopic(), FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $contact->setMessage(filter_var($contact->getMessage(), FILTER_SANITIZE_FULL_SPECIAL_CHARS));

            $contact->setSendingDate(new \DateTime());
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Form submitted successfully!');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('base/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/moderation/add-category', name: 'app_add_category')]
    public function add_category(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){
                $em->persist($category);
                $em->flush();
                $this->addFlash('notice','Catégorie ajoutée');
                return $this->redirectToRoute('app_add_category');
            }
        }


        return $this->render('base/add_category.html.twig', [
            'form' => $form->createView()

        ]);
    }
}
