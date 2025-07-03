<?php

namespace App\Command;

use App\Entity\Calendar;
use App\Entity\Event;
use App\Entity\Occurrence;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use ICal\ICal;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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

    private int $num_events = 0;

    private int $num_occurences = 0;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

//    protected function isGenerated(\ICal\Event $event): bool
//    {
//        return !empty($event->additionalProperties['rrule_array'])
//            && count($event->additionalProperties['rrule_array']) > 2
//            && $event->additionalProperties['rrule_array'][2] == ICal::RECURRENCE_EVENT;
//    }

    protected function findOrCreateEvent(\ICal\Event $icalEvent, Calendar $calendar): Event
    {
        $save = false;
        $event = $this->em->getRepository(Event::class)
            ->findOneBy(['uid' => $icalEvent->uid]);
        if (!$event) {
            $event = (new Event())
                ->setUid($icalEvent->uid)
                ->setCalendar($calendar);
            $save = true;
        }
        if (!empty($icalEvent->additionalProperties['rrule'])) {
            $event->setRrule($icalEvent->additionalProperties['rrule']);
            $save = true;
        }

        if ($save) {
            $this->num_events++;
            $this->em->persist($event);
            $this->em->flush();
        }

        return $event;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->em->getRepository(Occurrence::class)->deleteAll();
        $this->em->getRepository(Event::class)->deleteAll();

        $calendars = $this->em->getRepository(Calendar::class)->findAll();
        foreach ($calendars as $calendar) {
            /** @var Calendar $calendar */

            $ical = new ICal($calendar->getSource(), [
                'defaultTimeZone' => 'America/New_York',
                'defaultWeekStart' => 'SU',
            ]);

            foreach ($ical->events() as $icalEvent) {
                /** @var \ICal\Event $icalEvent */
                $event = $this->findOrCreateEvent($icalEvent, $calendar);
                $start = new DateTimeImmutable($icalEvent->dtstart_tz);
                $end = new DateTimeImmutable($icalEvent->dtend_tz);

                if (($end->getTimestamp() - $start->getTimestamp()) > (60 * 60 * 24)) {
                    $interval = DateInterval::createFromDateString('1 day');
                    $period = new DatePeriod($start, $interval, $end);
                    foreach ($period as $dt) {
                        $occurrence = (new Occurrence())
                            ->setEvent($event)
                            ->setTitle($icalEvent->summary)
                            ->setDescription($icalEvent->description)
                            ->setStart($dt)
                            ->setEnd($dt->add(DateInterval::createFromDateString('1 day')));
                        $this->em->persist($occurrence);
                        $this->em->flush();
                        $this->num_occurences++;
                    }
                } else {
                    $occurrence = (new Occurrence())
                        ->setEvent($event)
                        ->setTitle($icalEvent->summary)
                        ->setDescription($icalEvent->description)
                        ->setStart($start)
                        ->setEnd($end);
                    $this->em->persist($occurrence);
                    $this->em->flush();
                    $this->num_occurences++;
                }
            }
        }

        $io->success("{$this->num_events} events and {$this->num_occurences} occurrences created.");

        return Command::SUCCESS;
    }
}
