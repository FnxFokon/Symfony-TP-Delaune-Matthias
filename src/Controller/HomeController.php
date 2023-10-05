<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\BienRepository;
use App\Repository\CampingRepository;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BienRepository $bienRepository, CampingRepository $campingReposiroty): Response
    {
        $camp = $campingReposiroty->findAll();
        $biens = $bienRepository->getBiens();
        return $this->render('home/index.html.twig', [
            'camp' => $camp,
            'biens' => $biens,
        ]);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function bienById(BienRepository $bienRepository, int $id)
    {
        $bien = $bienRepository->findBienById($id);

        return $this->render('home/detailBien.html.twig', [
            'bien' => $bien,
        ]);
    }

    #[Route('/typebien/{id}', name: 'bien_by_typebien')]
    public function bienByTypebien(BienRepository $bienRepository, int $id)
    {
        $title = "L’Espadrille Volante";
        $biens = $bienRepository->findBienByTypeBien($id);
        return $this->render('home/index.html.twig', [
            'title' => $title,
            'biens' => $biens,
        ]);
    }

    #[Route('/doreservation/{id}', name: 'app_do_reservation')]
    public function doReservation(Request $request, ReservationRepository $reservationRepository, int $id)
    {
        $userId = $this->getUser()->getId();
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // $debutDate = 'reservation.dateBegin'; 
            // $dateBegin = new \DateTime($debutDate);
            // $timestampDateBegin = $dateBegin->getTimestamp();
            // $reservation->setDateBegin($timestampDateBegin);

            // $finDate = 'reservation.dateFin'; 
            // $dateFin = new \DateTime($finDate);
            // $timestampDateFin = $dateFin->getTimestamp();
            // $reservation->setDateFin($timestampDateFin);

            // TODO: Faire une instance de USER et de BIEN pour les envoyers dans les setter
            // TODO: Faire les calculs du prix final avec tous les reduction / prix ajouter pour les inserer dans un prix final


            $reservation->setUser($userId);
            $reservation->setBien($id);
            $reservationRepository->save($reservation, true);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('home/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }
}
