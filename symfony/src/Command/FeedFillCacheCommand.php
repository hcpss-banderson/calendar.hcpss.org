<?php

namespace App\Command;

use App\Entity\Feed;
use App\FeedService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:feed:fill-cache',
    description: 'Add a short description for your command',
)]
class FeedFillCacheCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FeedService $feedService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $feeds = $this->em->getRepository(Feed::class)->findAll();
        $num_feeds = 0;
        foreach ($feeds as $feed) {
            $this->feedService->fillCache($feed);
            $num_feeds++;
        }
        $io->success("{$num_feeds} feed caches filled.");
        return Command::SUCCESS;
    }
}
