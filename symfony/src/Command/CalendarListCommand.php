<?php

namespace App\Command;

use App\Entity\Calendar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:calendar:list',
    description: 'Add a short description for your command',
)]
class CalendarListCommand extends Command
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $table = new Table($output);
        $table->setHeaders(['Title', 'Slug', 'Sources']);
        $calendars = $this->em->getRepository(Calendar::class)->findAll();
        foreach ($calendars as $calendar) {
            $row = [
                $calendar->getTitle(),
                $calendar->getSlug(),
                implode("\n", $calendar->getSources()),
            ];
            $table->addRow($row);
        }
        $table->render();
        return Command::SUCCESS;
    }
}
