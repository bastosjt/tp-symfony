<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyPublicationsController extends AbstractController
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository
    ) {
    }

    #[Route('/mes-publications', name: 'app_my_publications')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $publications = $this->publicationRepository->findBy(
            ['author' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('my_publications/index.html.twig', [
            'publications' => $publications,
        ]);
    }
}
