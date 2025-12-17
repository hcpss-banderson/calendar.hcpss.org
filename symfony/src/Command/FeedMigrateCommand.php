<?php

namespace App\Command;

use App\Entity\Calendar;
use App\Entity\Feed;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:feed:migrate',
    description: 'Add a short description for your command',
)]
class FeedMigrateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $slugs = [
            'https://www.google.com/calendar/ical/howard.county.public.schools%40gmail.com/public/basic.ics' => 'hcpss_events',
            'https://www.google.com/calendar/ical/53tttfm4sd0vai54mnrnpn1q5o%40group.calendar.google.com/public/basic.ics' => 'boe_events',
            'https://calendar.google.com/calendar/ical/537on0svjl80bon1j075a8fep0%40group.calendar.google.com/public/basic.ics' => 'ab_days',
            'https://calendar.google.com/calendar/ical/58e6223a8e58d3fb75b3f3286bc566bd2f9e7bb631bed8499d4d219e4a454bb7%40group.calendar.google.com/public/basic.ics' => 'ra_days',
        ];
        $calendars = $this->em->getRepository(Calendar::class)->findAll();
        $num_feeds = 0;
        foreach ($calendars as $calendar) {
            foreach ($calendar->getSources() as $source) {
                if (!$this->em->getRepository(Feed::class)->findOneBy(['source' => $source])) {
                    $feed = new Feed();
                    $feed->setSlug($slugs[$source]);
                    $feed->setSource($source);
                    $feed->addCalendar($calendar);
                    $this->em->persist($feed);
                    $this->em->flush();
                    $num_feeds++;
                }
            }
        }

        $io = new SymfonyStyle($input, $output);
        $io->success("{$num_feeds} feeds created.");

        return Command::SUCCESS;
    }
}
