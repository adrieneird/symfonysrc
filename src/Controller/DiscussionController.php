<?php

namespace App\Controller;

use App\Entity\Discussion;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DiscussionController extends AbstractController
{
    #[Route('/', name: 'app_discussion')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $discussions = $doctrine->getRepository(Discussion::class)->findAll();
        
        return $this->render('discussion/index.html.twig', [
            'discussions' => $discussions,
        ]);
    }
    
    #[Route('/discussion/{id}', name: 'app_discussion_show')]
    public function show($id, ManagerRegistry $doctrine): Response
    {
        $discussion = $doctrine->getRepository(Discussion::class)->find($id);
        $messages = $discussion->getMessages();
        
        return $this->render('discussion/show.html.twig', [
            'discussion' => $discussion,
            'messages' => $messages,
        ]);
    }
    
    #[Route('/discussion_new', name: 'app_discussion_new')]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        // Créer un objet
        $discussion = new Discussion();
        
        // Créer un formulaire orienté objet
        $form = $this->createFormBuilder($discussion)
            ->add('titre', TextType::class)
            ->add('ajouter', SubmitType::class)
            ->getForm();

        // Traiter le $_POST
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            // Récupère les données du form
            $discussion = $form->getData();
            // Ajout du dateheure nécessaire pour sauver
            $discussion->setDateheure(new \Datetime());
            
            // Sauve dans la BDD avec l'ORM
            $entityManager = $doctrine->getManager();
            $entityManager->persist($discussion);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_discussion_show', ['id' => $discussion->getId()]);
        }

        // Affiche la vue avec le formulaire
        return $this->render('discussion/new.html.twig', [
            'form' => $form,
        ]);
    }
    
    #[Route('/discussion_edit/{id}', name: 'app_discussion_edit')]
    public function edit($id, Request $request, ManagerRegistry $doctrine): Response
    {
        // Créer un objet
        $discussion = $doctrine->getRepository(Discussion::class)->find($id);
        
        // Créer un formulaire orienté objet
        $form = $this->createFormBuilder($discussion)
            ->add('titre', TextType::class)
            ->add('modifier', SubmitType::class)
            ->getForm();

        // Traiter le $_POST
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            // Récupère les données du form
            $discussion = $form->getData();
            // Ajout du dateheure nécessaire pour sauver
            $discussion->setDateheure(new \Datetime());
            
            // Sauve dans la BDD avec l'ORM
            $entityManager = $doctrine->getManager();
            $entityManager->persist($discussion);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_discussion_show', ['id' => $discussion->getId()]);
        }

        // Affiche la vue avec le formulaire
        return $this->render('discussion/new.html.twig', [
            'form' => $form,
        ]);
    }
}
