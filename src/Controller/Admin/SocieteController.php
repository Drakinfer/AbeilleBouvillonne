<?php

namespace App\Controller\Admin;


use App\Entity\Societe;
use App\Form\SocieteType;
use App\Repository\SocieteRepository;
use App\Repository\CategorysRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/societe')]
class SocieteController extends AbstractController
{
    #[Route('/{id}/edit', name: 'societe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Societe $societe, EntityManagerInterface $entityManager, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $old_name_logo = $societe->getLogo();
        $path2 = $this->getParameter('upload_dir') . $old_name_logo;

        $old_name_banniere = $societe->getBanniere();
        $path3 = $this->getParameter('upload_dir') . $old_name_banniere;

        $old_name_favicon = $societe->getFavicon();
        $path4 = $this->getParameter('upload_dir') . $old_name_banniere;

        $form = $this->createForm(SocieteType::class, $societe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $logo = $form->get('logo')->getData();
            if ($logo) {
                if (file_exists($path2)) {
                    unlink($path2);
                }

                $logo_new_name = uniqid() . '.' . $logo->guessExtension();
                $logo->move($this->getParameter('upload_dir'), $logo_new_name);
                $societe->setLogo($logo_new_name);
            } else {
                $societe->setLogo($old_name_logo);
            }

            $banniere = $form->get('banniere')->getData();
            if ($banniere) {
                if (file_exists($path3)) {
                    unlink($path3);
                }
                $banniere_new_name = uniqid() . '.' . $banniere->guessExtension();
                $banniere->move($this->getParameter('upload_dir'), $banniere_new_name);
                $societe->setBanniere($banniere_new_name);
            } else {
                $societe->setBanniere($old_name_banniere);
            }

            $favicon = $form->get('favicon')->getData();
            if ($favicon) {
                if (file_exists($path4)) {
                    unlink($path4);
                }
                $favicon_new_name = uniqid() . '.' . $favicon->guessExtension();
                $favicon->move($this->getParameter('upload_dir'), $favicon_new_name);
                $societe->setFavicon($favicon_new_name);
            } else {
                $societe->setFavicon($old_name_favicon);
            }


            $entityManager->flush();

            return $this->redirectToRoute('sopciete_edit', ['id' => 1], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('societe/edit.html.twig', [
            'form' => $form,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }
}
