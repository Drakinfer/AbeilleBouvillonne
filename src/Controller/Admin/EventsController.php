<?php

namespace App\Controller\Admin;

use App\Entity\Events;
use App\Form\EventsType;
use App\Repository\CategorysRepository;
use App\Repository\EventsRepository;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/events')]
class EventsController extends AbstractController
{
    #[Route('/', name: 'events_index', methods: ['GET'])]
    public function index(EventsRepository $eventsRepository, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        return $this->render('events/index.html.twig', [
            'events' => $eventsRepository->findAll(),
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/new', name: 'events_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $event = new Events();
        $form = $this->createForm(EventsType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $image_new_name = uniqid() . '.' . $image->guessExtension();
            $image->move($this->getParameter('upload_dir'), $image_new_name);
            $event->setImage($image_new_name);
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('events_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('events/new.html.twig', [
            'event' => $event,
            'form' => $form,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}', name: 'events_show', methods: ['GET'])]
    public function show(Events $event, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        return $this->render('events/show.html.twig', [
            'event' => $event,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}/edit', name: 'events_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Events $event, EntityManagerInterface $entityManager, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $old_name_image = $event->getImage();
        $path = $this->getParameter('upload_dir') . $old_name_image;
        $form = $this->createForm(EventsType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                if (file_exists($path)) {
                    unlink($path);
                }
                $image_new_name = uniqid() . '.' . $image->guessExtension();
                $image->move($this->getParameter('upload_dir'), $image_new_name);
                $event->setImage($image_new_name);
            } else {
                $event->setImage($old_name_image);
            }

            $entityManager->flush();

            return $this->redirectToRoute('events_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('events/edit.html.twig', [
            'event' => $event,
            'form' => $form,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}', name: 'events_delete', methods: ['POST'])]
    public function delete(Request $request, Events $event, EntityManagerInterface $entityManager): Response
    {
        $picture_name = $event->getImage();
        $path = $this->getParameter('upload_dir') . $picture_name;
        unlink($path);
        $entityManager->remove($event);
        $entityManager->flush();

        return $this->redirectToRoute('events_index', [], Response::HTTP_SEE_OTHER);
    }
}
