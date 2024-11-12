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

        //Pour annuler une demande d'ami
        if($request->get('id')!=null){
            //On récupère l'utilisateur visé
            $id = $request->get('id');
            $userAsk = $userRepository->find($id);
            //Si on lui avait bien envoyé une demande, on la supprime
            if($userAsk){
                $this->getUser()->removeAsk($userAsk);
                $em->persist($this->getUser());
                $em->flush();
            }
        }

        //Pour refuser une demande d'ami
        if($request->get('idDecline')!=null){
            //On récupère la demande
            $id = $request->get('idDecline');
            $userDecline = $userRepository->find($id);
            //Si elle existe, on la supprime
            if($userDecline){
                $this->getUser()->removeUsersAsk($userDecline);
                $em->persist($this->getUser());
                $em->flush();
                $this->addFlash('notice','Invitation refusée');
                return $this->redirectToRoute('app_friends');
            }
        }

        //Pour accepter une demande d'ami
        if($request->get('idAccept')!=null){
            //On récupère la demande
            $id = $request->get('idAccept');
            $userAccept = $userRepository->find($id);
            //Si elle existe, on ajoute l'amitié (Accept) et on supprime la demande (Ask)
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

        //Pour supprimer une amitié
        if($request->get('idRemove')!=null){
            //On récupère l'utilisateur à retirer des amis
            $id = $request->get('idRemove');
            $userRemove = $userRepository->find($id);
            //S'il existe, on le supprimer des amis et réciproquement
            if($userRemove){
                $user = $this->getUser();
                $user->removeAccept($userRemove);
                $userRemove->removeAccept($user);

                
                //On récupère les fichiers que l'utilisateur a reçu
                $tab = $user->getFileShare();
                //Pour chaque fichier reçu par l'utilisateur, on regarde si le propriétaire est l'ancien ami
                foreach ($tab as $file) {
                    if($file->getUser() == $userRemove) {
                        //Si c'est le cas on supprime le partage
                        $file->removeShare($user);
                        $em->persist($file);
                    }
                }

                //Pour chaque fichier reçu par l'ancien ami, on regarde si le propriétaire est l'utilisateur
                $tab = $userRemove->getFileShare();
                foreach ($tab as $file) {
                    if($file->getUser() == $user) {
                        //Si c'est le cas on supprime le partage
                        $file->removeShare($userRemove);
                        $em->persist($file);
                    }
                }


                $em->persist($user);
                $em->flush();
                $this->addFlash('notice','Amitié supprimée, partages annulés');
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
