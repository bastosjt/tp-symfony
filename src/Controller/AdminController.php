<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PublicationRepository $publicationRepository,
    ) {
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->userRepository->findBy([], ['createdAt' => 'DESC']);
        $totalPublications = $this->publicationRepository->count([]);

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'totalPublications' => $totalPublications,
        ]);
    }
}
