<?php

namespace App\Controller;

use App\Services\Panier;
use App\Services\OrderManager;
use Symfony\Component\Mime\Email;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use App\Repository\CategorysRepository;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class AchatController extends AbstractController
{
    #[Route('/commande/votrecommande', name: 'validOrder')]
    public function confirmOrder(Panier $panier, OrderManager $orderManager, EntityManagerInterface $manager, CategorysRepository $categorysRepository, ProductsRepository $productsRepository, OrdersRepository $ordersRepository, MailerInterface $mailer, SocieteRepository $societeRepository)
    {
        $nb = count($panier->getDetailPanier());
        if ($nb == 0) {
            return $this->redirectToRoute('panier');
        }
        $wishedDate = $_GET['wishedDate'];
        $message = $_GET['message'];
        $commande = $orderManager->getOrder($panier);
        $commande->setWishedDelivery($wishedDate);
        $commande->setMessage($message);

        $manager->persist($commande);
        $tab_panier = $panier->getDetailPanier();

        foreach ($tab_panier as $row_panier) {
            $detail = $orderManager->getDetailPanier($commande, $row_panier);

            $manager->persist($detail);


            $product = $productsRepository->find($row_panier['product']->getId());
            $qty_stock = $product->getStock();
            $new_qty = $qty_stock - $row_panier['quantity'];
            $product->setStock($new_qty);
            $manager->persist($product);
        }
        $manager->flush();

        $panier->deletePanier();

        $mail = (new Email())
            ->from('abeillebouvillonne@gmail.com')
            ->to('abeillebouvillonne@gmail.com')
            ->subject('Nouvelle commande')
            ->text(
                "Une nouvelle commande vient d'être passée sur le site" . \PHP_EOL .
                    'Date de livraison demandée : ' . $wishedDate . \PHP_EOL .
                    'Message : ' . $message,
                'text/plain'
            );
        $mailer->send($mail);

        return $this->render('achat/order.html.twig', [
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }
}
