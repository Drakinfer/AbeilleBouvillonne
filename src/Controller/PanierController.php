<?php

namespace App\Controller;

use App\Repository\CategorysRepository;
use App\Repository\ProductsRepository;
use App\Repository\SocieteRepository;
use App\Services\Panier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier')]
    public function index(Panier $panier, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $total = $panier->getTotalPanier();
        $tab = $panier->getDetailPanier();
        $nb = count($panier->getDetailPanier());

        return $this->render('panier/panier.html.twig', [
            'items' => $tab,
            'montant' => $total,
            'categorys' => $categorysRepository->findAll(),
            'count' => $nb,
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}/index_add-panier', name: 'index_add_produit_panier')]
    public function addindex($id, Panier $panier, ProductsRepository $productsRepository): Response
    {
        $qty = $_GET['quantity'];
        $product = $productsRepository->find($id);
        if ($product->getOnOrder() === false && $qty > $product->getStock()) {
            return $this->redirectToRoute("home");
        } else {
            if ($qty > 0) {
                $panier->add($id, $qty);
                $panier->countPanier();
                return $this->redirectToRoute("home");
            } else {
                return $this->redirectToRoute("home");
            }
        }
    }

    #[Route('/{id}/add-panier', name: 'add_produit_panier')]
    public function add($id, Panier $panier): Response
    {
        $qty = $_GET['quantity'];
        if ($qty > 0) {
            $panier->add($id, $qty);
            $panier->countPanier();
            return $this->redirectToRoute("product_detail_show", ['id' => $id]);
        } else {
            return $this->redirectToRoute("home");
        }
    }

    #[Route('/{id}/add_1', name: 'add_1')]
    public function addOnePanier($id, Panier $panier): Response
    {
        $panier->addOne($id);
        $panier->countPanier();
        return $this->redirectToRoute("panier");
    }

    #[Route('/{id}/moins-1', name: 'moins_1')]
    public function RetirerQtyProduit($id, Panier $panier): Response
    {
        $panier->RetirerOne($id);
        return $this->redirectToRoute("panier");
    }

    #[Route('/{id}/delete-product-panier', name: 'delete_product_panier')]
    public function deleteProductPanier($id, Panier $panier): Response
    {
        $panier->deleteProductPanier($id);
        $panier->countPanier();
        return $this->redirectToRoute("panier");
    }

    #[Route('/delete-panier', name: 'delete_panier')]
    public function deletePanier(Panier $panier): Response
    {
        $panier->deletePanier();
        return $this->redirectToRoute('panier');;
    }

    #[Route('/{id}/change-panier', name: 'change_produit_panier')]
    public function changeQtyProduit($id, Panier $panier): Response
    {
        $qty = $_GET['quantity'];
        $panier->changeQty($id, $qty);
        return $this->redirectToRoute('panier');
    }
}
