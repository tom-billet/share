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
    public function contact(Request $request, EntityManagerInterface $em): Response
    {
        /*Préparer la création du contact*/
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){
                /*Ajout de la date actuelle de façon automatique*/
                $contact->setSendingDate(new \Datetime());
                /*Ajouter le contact*/
                $em->persist($contact);
                /*Enregistrer les modifications*/
                $em->flush();
                $this->addFlash('notice','Message envoyé');
                return $this->redirectToRoute('app_contact');
            }
        }

        /*Envoi du formulaire à la vue*/
        return $this->render('base/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/moderation/add-category', name: 'app_add_category')]
    public function add_category(Request $request, EntityManagerInterface $em): Response
    {
        /*Préparer la création de la category*/
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){
                /*Appliquer les modifications*/
                $em->persist($category);
                /*Enregistrer les modifications*/
                $em->flush();
                $this->addFlash('notice','Catégorie ajoutée');
                return $this->redirectToRoute('app_add_category');
            }
        }

        /*Envoi du formulaire à la vue*/
        return $this->render('base/add_category.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
