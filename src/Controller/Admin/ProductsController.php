<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\CategorysRepository;
use App\Repository\ProductsRepository;
use App\Repository\SocieteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'products_index', methods: ['GET'])]
    public function index(ProductsRepository $productsRepository, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        return $this->render('products/index.html.twig', [
            'products' => $productsRepository->findAll(),
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/new', name: 'products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTime();
            $product->setCreationDate($date);

            $image = $form->get('image')->getData();
            $image_new_name = uniqid() . '.' . $image->guessExtension();
            $image->move($this->getParameter('upload_dir'), $image_new_name);
            $product->setImage($image_new_name);

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/new.html.twig', [
            'product' => $product,
            'form' => $form,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}', name: 'products_show', methods: ['GET'])]
    public function show(Products $product, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}/edit', name: 'products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Products $product, EntityManagerInterface $entityManager, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $old_name_image = $product->getImage();
        $path = $this->getParameter('upload_dir') . $old_name_image;
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                if (file_exists($path)) {
                    unlink($path);
                }

                $image_new_name = uniqid() . '.' . $image->guessExtension();
                $image->move($this->getParameter('upload_dir'), $image_new_name);
                $product->setImage($image_new_name);
            } else {
                $product->setImage($old_name_image);
            }
            $entityManager->flush();

            return $this->redirectToRoute('products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}', name: 'products_delete', methods: ['POST'])]
    public function delete(Request $request, Products $product, EntityManagerInterface $entityManager): Response
    {
        $picture_name = $product->getImage();
        $path = $this->getParameter('upload_dir') . $picture_name;
        unlink($path);
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('products_index', [], Response::HTTP_SEE_OTHER);
    }
}
