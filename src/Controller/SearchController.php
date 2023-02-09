<?php

namespace App\Controller;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Doctrine\Persistence\ManagerRegistry;

class SearchController extends AbstractController
{
    #[Route('/search_nickname', name: 'app_search_nickname')]
    public function nickname(Request $request, ManagerRegistry $doctrine): Response
    {
        // Créer un tableau vide
        $search = [];
        
        // Créer un formulaire orienté objet
        $form = $this->createFormBuilder($search)
            ->add('nickname', SearchType::class)
            ->add('trouver', SubmitType::class)
            ->getForm();
        
        // Traiter le $_POST
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            // Récupère les données du form
            $search = $form->getData();
            
            // $search['nickname']
            // On cherche à trouver les messages du nickname
            $messages = $doctrine->getRepository(Message::class)->findByNickname($search['nickname']);
            
            return $this->render('search/index.html.twig', [ 'form' => $form, 'messages' => $messages]);
        }
        
        return $this->render('search/index.html.twig', [ 'form' => $form]);
    }
}
