<?php

namespace App\Controller;

use App\Repository\BienRepository;
use App\Repository\CampingRepository;
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

    // Création de la route pour mon esapce
    #[Route('/moncompte', name: 'app_compte')]
    public function compte()
    {
        // Si l'utilisateur est admin
        if ($this->isGranted('ROLE_ADMIN')) {
            // Redirection vers la page admin
            return $this->redirectToRoute('home/monCompteAdmin.html.twig', []);
        }

        // Si l'utilisateur est propriétaire
        if ($this->isGranted('ROLE_OWNER')) {
            // Redirection vers la page admin
            return $this->redirectToRoute('home/monCompteOwner.html.twig', []);
        }

        // Si l'utilisateur est user
        if ($this->isGranted('ROLE_USER')) {
            // Redirection vers la page admin
            return $this->redirectToRoute('home/monCompteUser.html.twig', []);
        }
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function gameById(BienRepository $bienRepository, int $id)
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
}
