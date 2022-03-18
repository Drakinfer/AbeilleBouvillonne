<?php

namespace App\Controller;

use App\Repository\CategorysRepository;
use App\Repository\SocieteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MentionsController extends AbstractController
{
    #[Route('/mentions', name: 'mentions')]
    public function index(CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        return $this->render('mentions/mentions.html.twig', [
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }
}
