<?php

namespace App\Command;

use App\Entity\Calendar;
use App\Entity\Occurrence;
use Doctrine\ORM\EntityManagerInterface;
use ICal\ICal;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:data:refresh',
    description: 'Add a short description for your command',
)]
class DataRefreshCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure(): void
    {
//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->em->getRepository(Occurrence::class)->deleteAll();

        $num_events = 0;
        $calendars = $this->em->getRepository(Calendar::class)->findAll();
        foreach ($calendars as $calendar) {
            /** @var Calendar $calendar */
            foreach ($calendar->getSources() as $source) {
                $ical = new ICal($source, [
                    'defaultTimeZone' => 'America/New_York',
                    'defaultWeekStart' => 'SU',
                ]);

                foreach ($ical->events() as $event) {
                    $occurrence = Occurrence::fromIcal($event);
                    $occurrence->setCalendar($calendar);

                    $this->em->persist($occurrence);
                    $num_events++;
                }

                $this->em->flush();
            }
        }

        $io->success("{$num_events} occurrences created.");

        return Command::SUCCESS;
    }
}
