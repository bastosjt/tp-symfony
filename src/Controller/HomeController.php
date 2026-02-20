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
        
        // Mélanger aléatoirement et prendre max 5 publications
        shuffle($allPublications);
        $publications = array_slice($allPublications, 0, 5);
        $hasMorePublications = count($allPublications) > 5;

        $publicationsData = [];

        foreach ($publications as $publication) {
            $author = $publication->getAuthor();
            $now = new \DateTimeImmutable();
            $diff = $now->diff($publication->getCreatedAt());
            
            $timeAgo = match(true) {
                $diff->days > 0 => $diff->days === 1 ? 'Il y a 1 jour' : "Il y a {$diff->days} jours",
                $diff->h > 0 => $diff->h === 1 ? 'Il y a 1 heure' : "Il y a {$diff->h} heures",
                $diff->i > 0 => $diff->i === 1 ? 'Il y a 1 minute' : "Il y a {$diff->i} minutes",
                default => 'À l\'instant'
            };

            $tags = [];
            foreach ($publication->getTags() as $tag) {
                $tags[] = [
                    'name' => $tag->getName(),
                ];
            }

            $publicationsData[] = [
                'authorName' => $author ? $author->getFirstName() . ' ' . $author->getLastName() : 'Utilisateur',
                'authorInitials' => $author ? strtoupper(substr($author->getFirstName(), 0, 1) . substr($author->getLastName(), 0, 1)) : '??',
                'avatarColor' => 'bg-blue-500',
                'timeAgo' => $timeAgo,
                'content' => $publication->getContent(),
                'tags' => $tags,
                'likesCount' => $publication->getReactions()->filter(fn($r) => $r->getType() === 'like')->count(),
                'commentsCount' => $publication->getCommentaires()->count(),
            ];
        }

        return $this->render('home/index.html.twig', [
            'publications' => $publicationsData,
            'hasMorePublications' => $hasMorePublications,
        ]);
    }
}