<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $allPublications = $this->publicationRepository->findAll();
        
        if (!$this->getUser()) {
            shuffle($allPublications);
            $publications = array_slice($allPublications, 0, 5);
            $hasMorePublications = count($allPublications) > 5;
        } else {

            $publications = $this->publicationRepository->findBy(
                [],
                ['createdAt' => 'DESC']
            );
            $hasMorePublications = false;
        }

        return $this->render('home/index.html.twig', [
            'publications' => $publications,
            'hasMorePublications' => $hasMorePublications,
        ]);
    }
}