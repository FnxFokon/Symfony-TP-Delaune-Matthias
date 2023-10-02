<?php

namespace App\Controller;

use App\Repository\BienRepository;
use App\Repository\TypeBienRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin/biens', name: 'app_biens')]
    public function biens(BienRepository $bienRepository): Response
    {
        $biens = $bienRepository->findBiensWithUser();
        return $this->render('admin/bien/indexBiens.html.twig', [
            'biens' => $biens,
        ]);
    }

    #[Route('/admin/users', name: 'app_users')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('admin/user/indexUsers.html.twig', [
            'users' => $users,
        ]);
    }
}
