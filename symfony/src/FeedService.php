<?php

namespace App;

use App\Entity\Feed;
use Doctrine\ORM\EntityManagerInterface;

class FeedService
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    public function fillCache(Feed $feed): self
    {
        $ics = file_get_contents($feed->getSource());
        $feed->setIcs($ics);
        $this->em->persist($feed);

        return $this;
    }
}
