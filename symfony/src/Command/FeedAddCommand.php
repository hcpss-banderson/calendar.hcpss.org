<?php

namespace App\Command;

use App\Entity\Calendar;
use App\Entity\Feed;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:feed:add',
    description: 'Add a short description for your command',
)]
class FeedAddCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('feed', InputArgument::REQUIRED, 'ICal feed URL to add.')
            ->addArgument('slug', InputArgument::REQUIRED, 'ICal feed slug.')
            ->addArgument('calendar', InputArgument::OPTIONAL, 'Calendar slug to add this feed to.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $calendar_slug = $input->getArgument('calendar');

        $feed = (new Feed())
            ->setSource($input->getArgument('feed'))
            ->setSlug($input->getArgument('slug'));

        if ($calendar_slug) {
            $calendar = $this->em->getRepository(Calendar::class)->findOneBy(['slug' => $calendar_slug]);
            if (!$calendar) {
                $io->error("Calendar {$calendar_slug} not found.");
                return Command::FAILURE;
            }
            $feed->addCalendar($calendar);
        }

        $this->em->persist($feed);
        $this->em->flush();

        $io->success('Feed added.');

        return Command::SUCCESS;
    }
}
