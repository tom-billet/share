<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\AddFriendType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Entity\User;

class FriendController extends AbstractController
{
    #[Route('/private/friends', name: 'app_friends')]
    public function Friends(Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {

        if($request->get('id')!=null){
            $id = $request->get('id');
            $userAsk = $userRepository->find($id);
            if($userAsk){
                $this->getUser()->removeAsk($userAsk);
                $em->persist($this->getUser());
                $em->flush();
            }
        }

        if($request->get('idDecline')!=null){
            $id = $request->get('idDecline');
            $userDecline = $userRepository->find($id);
            if($userDecline){
                $this->getUser()->removeUsersAsk($userDecline);
                $em->persist($this->getUser());
                $em->flush();
                $this->addFlash('notice','Invitation refusée');
                return $this->redirectToRoute('app_friends');
            }
        }

        if($request->get('idAccept')!=null){
            $id = $request->get('idAccept');
            $userAccept = $userRepository->find($id);
            if($userAccept){
                $this->getUser()->addAccept($userAccept);
                $userAccept->addAccept($this->getUser());
                $this->getUser()->removeUsersAsk($userAccept);
                $em->persist($this->getUser());
                $em->persist($userAccept);
                $em->flush();
                $this->addFlash('notice','Invitation acceptée');
                return $this->redirectToRoute('app_friends');
            }
        }

        if($request->get('idRemove')!=null){
            $id = $request->get('idRemove');
            $userRemove = $userRepository->find($id);
            if($userRemove){
                $this->getUser()->removeAccept($userRemove);
                $userRemove->removeAccept($this->getUser());
                $em->persist($this->getUser());
                $em->flush();
                $this->addFlash('notice','Amitié supprimée');
                return $this->redirectToRoute('app_friends');
            }
        }

        $form = $this->createForm(AddFriendType::class);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $friend = $userRepository->findOneBy(array('email'=>$form->get('email')->getData()));
                if(!$friend){
                    $this->addFlash('notice','Email introuvable');
                    return $this->redirectToRoute('app_friends');
                }
                else{
                    $this->getUser()->addAsk($friend);
                    $em->persist($this->getUser());
                    $em->flush();
                    $this->addFlash('notice','Invitation envoyée');
                    return $this->redirectToRoute('app_friends');
                }
            }
        }
        
        
        return $this->render('friend/friends.html.twig', [
            'form'=>$form
        ]);
    }


}
