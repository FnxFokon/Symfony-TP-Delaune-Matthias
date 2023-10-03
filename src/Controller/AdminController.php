<?php

namespace App\Controller;

use App\Entity\Bien;
use App\Entity\User;
use App\Form\BienType;
use App\Repository\BienRepository;
use App\Repository\UserRepository;
use App\Repository\TypeBienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/admin')]
class AdminController extends AbstractController
{
    // On déclare la propriété pour l'encodage
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    // ------------- PARTIE BIEN DE L ADMIN -------------
    #[Route('/bien', name: 'app_bien')]
    public function biens(BienRepository $bienRepository): Response
    {
        $biens = $bienRepository->findBiensWithUser();
        return $this->render('admin/bien/indexBiens.html.twig', [
            'biens' => $biens,
        ]);
    }

    #[Route('/bien/show/{id}', name: 'app_bien_show', methods: ['GET'])]
    public function showBien(Bien $bien): Response
    {
        return $this->render('admin/bien/show.html.twig', [
            'bien' => $bien,
        ]);
    }

    // Méthode pour ajouter un jeu
    #[Route('/bien/new', name: 'app_bien_new', methods: ['GET', 'POST'])]
    public function newBien(Request $request, BienRepository $bienRepository, TypeBienRepository $typeBienRepository, UserRepository $userRepository)
    {
        $bien = new Bien();
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image uploadée
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Si une image est uploadée, on récupère son nom d'origine
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // On genere un nouveau nom unique pour éviter d'écraser des images de même nom
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    // Déplacer l'image uploadée dans le dossier public/image
                    $imageFile->move(
                        // bien_images_directory est configuré dans config/services.yaml
                        $this->getParameter('bien_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }

                // On donne le nouveau nom pour la bdd
                $bien->setImagePath($newFilename);
            }

            // TODO: recupere l'ID du type de bien en fonction du form
            // $typebien = $form->get('')->get('')->getData();

            // On récupere l'id du type de bien pour le donner au bien
            // $bien->setTypeBien($typeBienRepository->find($typebien->getId()));


            // TODO: Recupérer l'id de user en fonction du form
            // $user = $form->get('')->get('')->getData();

            // On récupère l'id de infoUser
            // $bien->setUser($userRepository->find($user->getId()));


            // On enregistre le bien dans la bdd
            $bienRepository->save($bien, true);

            return $this->redirectToRoute("app_bien", [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/bien/new.html.twig', [
            'bien' => $bien,
            'form' => $form,
        ]);
    }


    #[Route('/bien/edit/{id}', name: 'app_bien_edit', methods: ['GET', 'POST'])]
    public function editBien(Request $request, Bien $bien, BienRepository $bienRepository): Response
    {
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $bienRepository->save($bien, true);

            // Gestion de l'image uploadée
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Si une image est uploadée, on récupère son nom d'origine
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // On genere un nouveau nom unique pour éviter d'écraser des images de même nom
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    // Déplacer l'image uploadée dans le dossier public/image
                    $imageFile->move(
                        // game_images_directory est configuré dans config/services.yaml
                        $this->getParameter('bien_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }

                // On donne le nouveau nom pour la bdd
                $bien->setImagePath($newFilename);
            }

            return $this->redirectToRoute('app_bien', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('admin/bien/edit.html.twig', [
            'bien' => $bien,
            'form' => $form,
        ]);
    }

    #[Route('/bien/delete/{id}', name: 'app_bien_delete', methods: ['POST'])]
    public function deleteBien(Request $request, Bien $bien, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $bien->getId(), $request->request->get('_token'))) {
            $entityManager->remove($bien);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_biens', [], Response::HTTP_SEE_OTHER);
    }

    //  ------------- PARTIE USER DE L ADMIN -------------


    #[Route('/user', name: 'app_user')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('admin/user/indexUsers.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/show/{id}', name: 'app_user_show', methods: ['GET'])]
    public function showUser(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/edit/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function editUser(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $user->setPassword($this->encoder->hashPassword($user, $plainPassword));
            }

            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/user/user/delete/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_users', [], Response::HTTP_SEE_OTHER);
    }
}
