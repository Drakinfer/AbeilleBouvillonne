<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\CategorysRepository;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/users')]
class UserController extends AbstractController
{
    #[Route('/', name: 'users_index', methods: ['GET'])]
    public function index(CategorysRepository $categorysRepository, UserRepository $userRepository, SocieteRepository $societeRepository): Response
    {
        return $this->render('user/users.html.twig', [
            'users' => $userRepository->findAll(),
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}/modo', name: 'setModo', methods: ['GET'])]
    public function setModo($id, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $user = $userRepository->find($id);
        $user->setRoles(["ROLE_ADMIN"]);
        $entityManager->flush();
        return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
    }
}
