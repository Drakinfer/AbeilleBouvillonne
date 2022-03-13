<?php

namespace App\Controller\Admin;

use App\Entity\Categorys;
use App\Form\CategorysType;
use App\Repository\CategorysRepository;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorys')]
class CategorysController extends AbstractController
{
    #[Route('/', name: 'categorys_index', methods: ['GET'])]
    public function index(CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        return $this->render('categorys/index.html.twig', [
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/new', name: 'categorys_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SocieteRepository $societeRepository): Response
    {
        $category = new Categorys();
        $form = $this->createForm(CategorysType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('categorys_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorys/new.html.twig', [
            'category' => $category,
            'form' => $form,
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}', name: 'categorys_show', methods: ['GET'])]
    public function show(Categorys $category, SocieteRepository $societeRepository): Response
    {
        return $this->render('categorys/show.html.twig', [
            'category' => $category,
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}/edit', name: 'categorys_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorys $category, EntityManagerInterface $entityManager, SocieteRepository $societeRepository): Response
    {
        $form = $this->createForm(CategorysType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('categorys_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorys/edit.html.twig', [
            'category' => $category,
            'form' => $form,
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}', name: 'categorys_delete', methods: ['POST'])]
    public function delete(Request $request, Categorys $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorys_index', [], Response::HTTP_SEE_OTHER);
    }
}
