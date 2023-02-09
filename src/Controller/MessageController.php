<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Discussion;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $messages = $doctrine->getRepository(Message::class)->findAll();
        
        return $this->render('message/index.html.twig', [
            'messages' => $messages,
        ]);
    }
    
    #[Route('/message/{id}', name: 'app_message_show')]
    public function show($id, ManagerRegistry $doctrine): Response
    {
        $message = $doctrine->getRepository(Message::class)->find($id);
        
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }
    
    #[Route('/message_new/{id_discussion}', name: 'app_message_new')]
    public function new($id_discussion, Request $request, ManagerRegistry $doctrine): Response
    {
        // Créer un objet
        $message = new Message();
        $message->setIdDiscussion($doctrine->getRepository(Discussion::class)->find($id_discussion));
        
        // Créer un formulaire orienté objet
        $form = $this->createFormBuilder($message)
            ->add('nickname', TextType::class)
            ->add('texte', TextType::class)
            ->add('id_discussion', EntityType::class, [
                // looks for choices from this entity
                'class' => Discussion::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'titre',
            ])
            ->add('ajouter', SubmitType::class)
            ->getForm();

        // Traiter le $_POST
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            // Récupère les données du form
            $message = $form->getData();
            // Ajout du dateheure nécessaire pour sauver
            $message->setDateheure(new \Datetime());
            // Ajout de la discussion nécessaire pour sauver
            //$message->setIdDiscussion($doctrine->getRepository(Discussion::class)->find(1));
            
            // Sauve dans la BDD avec l'ORM
            $entityManager = $doctrine->getManager();
            $entityManager->persist($message);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_discussion_show', ['id' => $message->getIdDiscussion()->getId()]);
        }

        // Affiche la vue avec le formulaire
        return $this->render('message/new.html.twig', [
            'form' => $form,
        ]);
    }
    
    #[Route('/message_edit/{id}', name: 'app_message_edit')]
    public function edit($id, Request $request, ManagerRegistry $doctrine): Response
    {
        // Créer un objet
        $message = $doctrine->getRepository(Message::class)->find($id);
        
        // Créer un formulaire orienté objet
        $form = $this->createFormBuilder($message)
            ->add('nickname', TextType::class)
            ->add('texte', TextType::class)
            ->add('id_discussion', EntityType::class, [
                // looks for choices from this entity
                'class' => Discussion::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'titre',
            ])
            ->add('ajouter', SubmitType::class)
            ->getForm();

        // Traiter le $_POST
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            // Récupère les données du form
            $message = $form->getData();
            // Ajout du dateheure nécessaire pour sauver
            $message->setDateheure(new \Datetime());
            // Ajout de la discussion nécessaire pour sauver
            //$message->setIdDiscussion($doctrine->getRepository(Discussion::class)->find(1));
            
            // Sauve dans la BDD avec l'ORM
            $entityManager = $doctrine->getManager();
            $entityManager->persist($message);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_discussion_show', ['id' => $message->getIdDiscussion()->getId()]);
        }

        // Affiche la vue avec le formulaire
        return $this->render('message/new.html.twig', [
            'form' => $form,
        ]);
    }
}
