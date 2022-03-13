<?php

namespace App\Services;

use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Panier
{
    private $session;

    private $productRepository;

    public function __construct(SessionInterface $session, ProductsRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public function getPanier()
    {
        return $this->session->get('panier', []);
    }


    public function add($id, $qty)
    {
        $panier = $this->getPanier();
        if (!empty($panier[$id])) {
            $panier[$id] = $panier[$id] + (int) $qty;
        } else {
            $panier[$id] = (int) $qty;
        }

        $this->session->set('panier', $panier);
    }

    public function addOne($id)
    {
        $panier = $this->getPanier();
        if (!empty($panier[$id])) {
            $panier[$id] = $panier[$id] + 1;
        } else {
            $panier[$id] = 1;
        }

        $this->session->set('panier', $panier);
    }

    public function deleteProductPanier($id)
    {
        $panier = $this->getPanier();
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);
    }

    public function deletePanier()
    {
        return $this->session->remove('panier');
    }

    public function RetirerOne($id)
    {
        $panier = $this->getPanier();

        if ($panier[$id] > 1) {
            $panier[$id] = $panier[$id] - 1;
        } else {
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);
    }

    public function changeQty($id, $qty)
    {
        $panier = $this->getPanier();

        if ($qty > 1) {
            $panier[$id] = $qty;
        } else {
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);
    }

    public function getDetailPanier()
    {
        $panier = $this->getPanier();
        $new_panier = [];
        foreach ($panier as $id => $qty) {
            $produit = $this->productRepository->find($id);
            if ($produit) {
                $new_panier[]  = [

                    'product' => $produit,
                    'quantity' => $qty,
                    'total' => $qty * $produit->getPrix(),
                ];
            } else {
                unset($panier[$id]);
            }
        }
        return $new_panier;

        $this->session->set('panier', $new_panier);
    }

    public function getTotalPanier()
    {
        $panier = $this->getDetailPanier();
        $somme_panier = 0;
        foreach ($panier as $key) {
            $somme_panier = $somme_panier + $key['total'];
        }
        return $somme_panier;
    }

    public function countPanier()
    {
        $panier = $this->getPanier();
        $this->session->set('count', count($panier));
    }
}
