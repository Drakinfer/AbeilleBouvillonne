<?php

namespace App\Controller;

use DateTime;
use App\Form\MdpType;
use App\Entity\Orders;
use App\Entity\Contact;
use App\Form\ProfilType;
use App\Form\MessageType;
use Symfony\Component\Mime\Email;
use App\Repository\OrdersRepository;
use App\Repository\CategorysRepository;
use App\Repository\SocieteRepository;

use function PHPUnit\Framework\isEmpty;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil')]
    public function index(CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $user = $this->getUser();
        return $this->render('profil/profil.html.twig', [
            'user' => $user,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/edit_user', name: 'edit_user')]
    public function editUser(Request $request, EntityManagerInterface $entityManager, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('profil', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('profil/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/edit_password', name: 'edit_password')]
    public function editPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(MdpType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData(),
                )
            );
            $entityManager->flush();

            return $this->redirectToRoute('profil', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('profil/editpassword.html.twig', [
            'user' => $user,
            'form' => $form,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/myorders/{statut?}', name: 'myorders')]
    public function myorders(CategorysRepository $categorysRepository, ?string $statut, OrdersRepository $ordersRepository, SocieteRepository $societeRepository)
    {
        $user = $this->getUser();
        $order = $user->getOrders();
        if (is_null($statut)) {
            return $this->render('profil/mescommandes.html.twig', [
                'orders' => $order,
                'categorys' => $categorysRepository->findAll(),
                'statut' => $statut,
                'societe' => $societeRepository->find(1),
            ]);
        } else {
            $orders = $ordersRepository->findBy(['statut' => $statut]);
            return $this->render('profil/mescommandes.html.twig', [
                'orders' => $orders,
                'categorys' => $categorysRepository->findAll(),
                'statut' => $statut,
                'societe' => $societeRepository->find(1),
            ]);
        }
    }

    #[Route('/show/{id}', name: 'myorder_show', methods: ['GET'])]
    public function show($id, OrdersRepository $ordersRepository, CategorysRepository $categorysRepository, SocieteRepository $societeRepository): Response
    {
        $order = $ordersRepository->find($id);

        return $this->render('order/show.html.twig', [
            'order' => $order,
            'products' => $order->getOrderDetails(),
            'categorys' => $categorysRepository->findAll(),
            'message' => $order->getContacts(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/{id}/newmessage', name: 'new_message')]
    public function newMessage($id, CategorysRepository $categorysRepository, Request $request, EntityManagerInterface $manager, OrdersRepository $ordersRepository, MailerInterface $mailer, AccessDecisionManagerInterface $accessDecisionManager, SocieteRepository $societeRepository): Response
    {
        $order = $ordersRepository->find($id);
        $user = $this->getUser();
        $date = new DateTime();
        $message = new Contact();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setUser($user);
            $message->setOrders($order);
            $message->setDate($date);
            $message->setView(false);
            $manager->persist($message);
            $manager->flush();

            $email = $order->getUser()->getEmail();
            $emailadmin = $societeRepository->find(1)->getMail();
            $nom = $user->getName();
            $prenom = $user->getFirstName();
            $id = $order->getId();
            $contactFormData = $form->getData();
            $title = $contactFormData->getTitle();
            $comm = $contactFormData->getMessage();

            if ($accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
                $mail = (new Email())
                    ->from($emailadmin)
                    ->to($email)
                    ->subject('Nouveau message concernant votre commande n°' . $id . ' : ' . $title)
                    ->text(
                        "Message de l'administrateur :" . \PHP_EOL .
                            $comm,
                        'text/plain'
                    );
                $mailer->send($mail);
            } else {

                $mail = (new Email())
                    ->from($emailadmin)
                    ->to($emailadmin)
                    ->subject('Nouveau message concernant la commande n°' . $id . ' de ' . $nom . ' ' . $prenom . ' : ' . $title)
                    ->text(
                        "Message du client :" . \PHP_EOL .
                            $comm,
                        'text/plain'
                    );
                $mailer->send($mail);
            }


            return $this->redirectToRoute('myorder_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
            'categorys' => $categorysRepository->findAll(),
            'societe' => $societeRepository->find(1),
        ]);
    }

    #[Route('/order/{id}/cancelled', name: 'cancelled')]
    public function cancelled(Orders $order, EntityManagerInterface $manager, MailerInterface $mailer, SocieteRepository $societeRepository)
    {
        $order->setStatut('Annulée');
        $manager->flush();
        $user = $this->getUser();
        $nom = $user->getName();
        $prenom = $user->getFirstName();
        $id = $order->getId();
        $email = $societeRepository->find(1)->getMail();
        $mail = (new Email())
            ->from($email)
            ->to($email)
            ->subject('Commande annulée')
            ->text(
                $nom . ' ' . $prenom . ' a annulé sa commande n°' . $id,
                'text/plain'
            );
        $mailer->send($mail);
        return $this->redirectToRoute('myorders', [], Response::HTTP_SEE_OTHER);
    }
}
