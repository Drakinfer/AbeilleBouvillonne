<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
use Symfony\Component\Mime\Email;
use App\Repository\OrdersRepository;
use App\Repository\CategorysRepository;
use App\Repository\ProductsRepository;
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
        $order->setStatut('Pr??te');
        $manager->flush();

        $id = $order->getId();
        $email = $order->getUser()->getEmail();
        $mail = (new Email())
            ->from('abeillebouvillonne@gmail.com')
            ->to($email)
            ->subject('Commande n??' . $id . ' pr??te')
            ->text(
                'Votre commande n??' . $id . ' est pr??te.' . \PHP_EOL .
                    "Vous pouvez convenir d'un rendez-vous avec le vendeur directement sur le site ou au ",
                'text/plain'
            );
        $mailer->send($mail);


        return $this->redirectToRoute('order', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/order/{id}/delivered', name: 'delivered')]
    public function delivered(Orders $order, EntityManagerInterface $manager, MailerInterface $mailer)
    {
        $order->setStatut('Livr??e');
        $manager->flush();

        $id = $order->getId();
        $email = $order->getUser()->getEmail();
        $mail = (new Email())
            ->from('abeillebouvillonne@gmail.com')
            ->to($email)
            ->subject('Commande n??' . $id . ' livr??e')
            ->text(
                'Votre commande n??' . $id . ' a ??t?? livr??e.' . \PHP_EOL .
                    "Si ce n'est pas le cas, vous pouvez nous consulter ?? partir du site ou au ",
                'text/plain'
            );
        $mailer->send($mail);

        return $this->redirectToRoute('order', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/order/{id}/cancelledadmin', name: 'cancelledadmin')]
    public function cancelled(Orders $order, EntityManagerInterface $manager, MailerInterface $mailer, ProductsRepository $productsRepository)
    {
        $order->setStatut('Annul??e');
        $products = $order->getOrderDetails();
        foreach ($products as $product) {
            $name = $product->getName();
            $qty = $product->getQuantity();
            $item = $productsRepository->findbyName($name);
            $item[0]->setStock($qty);
        }
        $manager->flush();
        $id = $order->getId();
        $email = $order->getUser()->getEmail();
        $mail = (new Email())
            ->from('abeillebouvillonne@gmail.com')
            ->to($email)
            ->subject('Commande n??' . $id . ' annul??e')
            ->text(
                'Le vendeur a annul?? votre commande n??' . $id . \PHP_EOL .
                    'Vous pouvez consulter la commande sur le site pour voir la raison ou nous contacter',
                'text/plain'
            );
        $mailer->send($mail);
        return $this->redirectToRoute('order', [], Response::HTTP_SEE_OTHER);
    }
}
