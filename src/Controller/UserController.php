<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserTypeUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{
    // On déclare la propriété pour l'encodage
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    // ---------------- ---------------- ----------------

    // ----------------- PARTIE OWNER  ------------------

    // ---------------- ---------------- ----------------

    #[Route('/owner', name: 'app__owner_bien')]
    public function bienByOwner(UserRepository $userRepository): Response
    {
        $id = $this->getUser()->getId();
        $biens = $userRepository->getOwnerBien($id);
        return $this->render('owner/indexBien.html.twig', [
            'biens' => $biens,
        ]);
    }

    #[Route('/owner/reservation/{id}', name: 'app__owner_reservation')]
    public function reservationByBien(ReservationRepository $reservationRepository, int $id): Response
    {
        $reservations = $reservationRepository->getReservationByBienId($id);
        return $this->render('owner/reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }


    // ---------------- ---------------- ----------------

    // ----------------- PARTIE USER  -------------------

    // ---------------- ---------------- ----------------

    #[Route('/user', name: 'app_compte')]
    public function reservation(UserRepository $userRepository): Response
    {

        $id = $this->getUser()->getId();
        $user = $userRepository->findMe($id);
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/reservation/{id}', name: 'app_show_reservation', methods: ['GET'])]
    public function showUser(ReservationRepository $reservationRepository, int $id): Response
    {
        $reservations = $reservationRepository->getReservationByUserId($id);
        return $this->render('user/reservation.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/user/edit/{id}', name: 'app_edit_user', methods: ['GET', 'POST'])]
    public function editUser(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserTypeUser::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $user->setPassword($this->encoder->hashPassword($user, $plainPassword));
            }

            $userRepository->save($user, true);

            return $this->redirectToRoute('app_compte', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/user/user/delete/{id}', name: 'app_delete_user', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_compte', [], Response::HTTP_SEE_OTHER);
    }
}
