<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
use Symfony\Component\Mime\Email;
use App\Repository\OrdersRepository;
use App\Repository\CategorysRepository;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class OrderController extends AbstractController
{
    #[Route('/order/{statut?}', name: 'order')]
    public function index(?string $statut, CategorysRepository $categorysRepository, OrdersRepository $ordersRepository, SocieteRepository $societeRepository): Response
    {
        if (is_null($statut)) {
            return $this->render('order/index.html.twig', [
                'orders' => $ordersRepository->findAll(),
                'categorys' => $categorysRepository->findAll(),
                'statut' => $statut,
                'societe' => $societeRepository->find(1),
            ]);
        } else {
            $orders = $ordersRepository->findBy(['statut' => $statut]);
            return $this->render('order/index.html.twig', [
                'orders' => $orders,
                'categorys' => $categorysRepository->findAll(),
                'statut' => $statut,
                'societe' => $societeRepository->find(1),
            ]);
        }
    }

    #[Route('/show/{id}', name: 'order_show', methods: ['GET'])]
    public function show(Orders $order, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
            'products' => $order->getOrderDetails(),
            'categorys' => $categorysRepository->findAll(),
            'message' => $order->getContacts(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/order/{id}/ready', name: 'ready')]
    public function ready(Orders $order, EntityManagerInterface $manager, MailerInterface $mailer)
    {
        $order->setStatut('Prête');
        $manager->flush();

        $id = $order->getId();
        $email = $order->getUser()->getEmail();
        $mail = (new Email())
            ->from('abeillebouvillonne@gmail.com')
            ->to($email)
            ->subject('Commande n°' . $id . ' prête')
            ->text(
                'Votre commande n°' . $id . ' est prête.' . \PHP_EOL .
                    "Vous pouvez convenir d'un rendez-vous avec le vendeur directement sur le site ou au ",
                'text/plain'
            );
        $mailer->send($mail);


        return $this->redirectToRoute('order', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/order/{id}/delivered', name: 'delivered')]
    public function delivered(Orders $order, EntityManagerInterface $manager, MailerInterface $mailer)
    {
        $order->setStatut('Livrée');
        $manager->flush();

        $id = $order->getId();
        $email = $order->getUser()->getEmail();
        $mail = (new Email())
            ->from('abeillebouvillonne@gmail.com')
            ->to($email)
            ->subject('Commande n°' . $id . ' livrée')
            ->text(
                'Votre commande n°' . $id . ' a été livrée.' . \PHP_EOL .
                    "Si ce n'est pas le cas, vous pouvez nous consulter à partir du site ou au ",
                'text/plain'
            );
        $mailer->send($mail);

        return $this->redirectToRoute('order', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/order/{id}/cancelledadmin', name: 'cancelledadmin')]
    public function cancelled(Orders $order, EntityManagerInterface $manager, MailerInterface $mailer)
    {
        $order->setStatut('Annulée');
        $manager->flush();
        $id = $order->getId();
        $email = $order->getUser()->getEmail();
        $mail = (new Email())
            ->from('abeillebouvillonne@gmail.com')
            ->to($email)
            ->subject('Commande n°' . $id . ' annulée')
            ->text(
                'Le vendeur a annulé votre commande n°' . $id . \PHP_EOL .
                    'Vous pouvez consulter la commande sur le site pour voir la raison ou nous contacter',
                'text/plain'
            );
        $mailer->send($mail);
        return $this->redirectToRoute('order', [], Response::HTTP_SEE_OTHER);
    }
}
