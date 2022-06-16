<?php

namespace App\Command;

use App\Entity\Calendar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:calendar:add',
    description: 'Add a short description for your command',
)]
class CalendarAddCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param string|null $name
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('title', InputArgument::REQUIRED, 'Human readable name of the calendar.')
            ->addArgument('slug', InputArgument::REQUIRED, 'Machine readable slug.')
            ->addArgument('sources', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'A list of ical sources.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $title = $input->getArgument('title');
        $slug = $input->getArgument('slug');
        $sources = $input->getArgument('sources');

        $calendar = (new Calendar())
            ->setTitle($title)
            ->setSlug($slug)
            ->setSources($sources);

        $this->em->persist($calendar);
        $this->em->flush();

        $io->success("Calendar {$calendar->getId()} created.");

        return Command::SUCCESS;
    }
}
