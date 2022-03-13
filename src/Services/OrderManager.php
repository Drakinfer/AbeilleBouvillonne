<?php

namespace App\Services;

use DateTime;
use App\Entity\Orders;
use App\Entity\OrderDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class OrderManager
{
    private $security;
    private $manager;

    public function __construct(Security $security, EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->manager = $manager;
    }

    public function getOrder(Panier $panier)
    {
        $commande = new Orders();
        $user = $this->security->getUser();
        $commande->setUser($user);
        $date_com = new DateTime();
        $commande->setOrderDate($date_com);
        $ttc = $panier->getTotalPanier();
        $commande->setTotal($ttc);
        $commande->setStatut('Commandé');
        return $commande;
    }

    public function getDetailPanier($commande, $panier)
    {
        $detail_commande = new OrderDetail();
        $detail_commande->setCommande($commande);
        $detail_commande->setReference($panier['product']->getReference());
        $detail_commande->setName($panier['product']->getName());
        $detail_commande->setQuantity($panier['quantity']);
        $detail_commande->setUnitPrice($panier['product']->getPrix());
        $detail_commande->setTotal($panier['total']);

        return $detail_commande;
    }
}
