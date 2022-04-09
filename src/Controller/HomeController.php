<?php

namespace App\Controller;

use App\Entity\Events;
use App\Entity\Products;
use App\Repository\ProductsRepository;
use App\Repository\CategorysRepository;
use App\Repository\EventsRepository;
use App\Repository\PresentationRepository;
use App\Repository\SocieteRepository;
use DateTime;
use PhpParser\Node\Expr\New_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CategorysRepository $categorysRepository, ProductsRepository $productsRepository, EventsRepository $eventsRepository, SocieteRepository $societeRepository): Response
    {
        $actual_time = new DateTime();
        $actual_time->format('Y-m-d');

        $events = $eventsRepository->findNextEvents($actual_time);
        $three_events = array_slice($events, 0, 3);

        return $this->render('home/index.html.twig', [
            'categorys' => $categorysRepository->findAll(),
            'productsStock' => $productsRepository->findStockSup0(),
            'events' => $three_events,
            'productsOnOrder' => $productsRepository->findOnOrder(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}/show', name: 'product_detail_show', methods: ['GET'])]
    public function showProduct(Products $product, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {

        return $this->render('products/show.html.twig', [
            'product' => $product,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/home-filtre/{cat}', name: 'nav_filtre')]
    public function filtre($cat, ProductsRepository $productRepository, EventsRepository $eventsRepository, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $actual_time = new DateTime();
        $actual_time->format('Y-m-d');

        $events = $eventsRepository->findNextEvents($actual_time);
        $three_events = array_slice($events, 0, 3);
        $category = $categorysRepository->findOneBy(['name' => $cat]);
        $product = $productRepository->findBy(['category' => $category]);
        return $this->render('home/index.html.twig', [
            'categorys' => $categorysRepository->findAll(),
            'productsStock' => $productRepository->findStockSup0Cat($category),
            'events' => $three_events,
            'productsOnOrder' => $productRepository->findOnOrderCat($category),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}/showevent', name: 'event_detail', methods: ['GET'])]
    public function showEvent(Events $event, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {

        return $this->render('events/show.html.twig', [
            'event' => $event,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/recherche', name: 'recherche')]
    public function recherche(ProductsRepository $productRepository, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $name = $_POST['recherche'];
        $products = $productRepository->rechercheByName($name);
        return $this->render('home/recherche.html.twig', [
            'categorys' => $categorysRepository->findAll(),
            'products' => $products,
            'name' => $name,
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/onStock', name: 'onStockAll')]
    public function OnStockAll(ProductsRepository $productRepository, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $products = $productRepository->findAllStockSup0();
        return $this->render('home/products.html.twig', [
            'categorys' => $categorysRepository->findAll(),
            'products' => $products,
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/onOrder', name: 'onOrderAll')]
    public function OnOrderAll(ProductsRepository $productRepository, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $products = $productRepository->findAllOnOrder();
        return $this->render('home/products.html.twig', [
            'categorys' => $categorysRepository->findAll(),
            'products' => $products,
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/eventsList', name: 'eventslist')]
    public function Events(EventsRepository $eventsRepository, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $date = new DateTime;
        $events = $eventsRepository->findNextEvents($date);
        return $this->render('home/events.html.twig', [
            'categorys' => $categorysRepository->findAll(),
            'events' => $events,
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/events/{id}', name: 'event_show', methods: ['GET'])]
    public function show(Events $event, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        return $this->render('home/event_show.html.twig', [
            'event' => $event,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/qui-sommes-nous?}', name: 'presentation', methods: ['GET'])]
    public function presentation(CategorysRepository $categorysRepository, SocieteRepository $societeRepository, PresentationRepository $presentationRepository): Response
    {
        return $this->render('home/presentation.html.twig', [
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
            'presentation' => $presentationRepository->findAllTriPosition(),
        ]);
    }
}
