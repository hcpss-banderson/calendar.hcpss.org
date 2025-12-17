<?php

namespace App\Controller;

use App\Entity\Feed;
use App\FeedService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FeedController extends AbstractController
{
    #[Route('/feed/{slug}.ics', name: 'app_feed')]
    public function feed(EntityManagerInterface $em, FeedService $feedService, string $slug): Response
    {
        $feed = $em->getRepository(Feed::class)->findOneBy(['slug' => $slug]);
        if (!$feed) {
            return new Response('Calendar not found.', 404);
        }

        if (!$feed->getIcs()) {
            $feedService->fillCache($feed);
        }

        return new Response($feed->getIcs());
    }
}
