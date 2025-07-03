<?php

namespace App\Model;

use App\Entity\Event;
use App\Entity\Occurrence;
use DateTimeInterface;

class Day {

    public const MODIFIERS_A_DAY = 'a-day';
    public const MODIFIERS_B_DAY = 'b-day';
    public const PARTIAL_DAY = 'partial-day';
    public const CLOSED_DAY = 'closed-day';

    /**
     * @var Occurrence[]
     */
    private $occurrences;

    /**
     * @var string[]
     */
    private $modifiers;

    /**
     * @var DateTimeInterface
     */
    private $date;

    public function __construct(DateTimeInterface $date)
    {
        $this->date = $date;
        $this->occurrences = [];
        $this->modifiers = [];
    }

    /**
     * @return Occurrence[]
     */
    public function getOccurrences(): array
    {
        return $this->occurrences;
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        $events = [];
        foreach ($this->occurrences as $occurrence) {
            $event = $occurrence->getEvent();
            $events[$event->getId()] = $event;
        }
        return $events;
    }

    /**
     * @param Occurrence[] $occurrences
     * @return Day
     */
    public function setOccurrences(array $occurrences): Day
    {
        $this->occurrences = $occurrences;
        return $this;
    }

    /**
     * @param Occurrence $occurrence
     * @return $this
     */
    public function addOccurrence(Occurrence $occurrence): Day
    {
        if ($occurrence->getTitle() == 'A') {
            $this->addModifier(Day::MODIFIERS_A_DAY);
        } else if ($occurrence->getTitle() == 'B') {
            $this->addModifier(Day::MODIFIERS_B_DAY);
        } else if (str_contains(strtolower($occurrence->getTitle()), '3 hours early')) {
            $this->addModifier(Day::PARTIAL_DAY);
            $this->occurrences[] = $occurrence;
        } else if (
            str_contains(strtolower($occurrence->getTitle()), 'schools and offices closed') ||
            str_contains(strtolower($occurrence->getTitle()), 'schools closed') ||
            str_contains(strtolower($occurrence->getTitle()), 'schools & offices closed')
        ) {
            $this->addModifier(Day::CLOSED_DAY);
            $this->occurrences[] = $occurrence;
        } else {
            $this->occurrences[] = $occurrence;
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    /**
     * @param string[] $modifiers
     * @return Day
     */
    public function setModifiers(array $modifiers = []): Day
    {
        $this->modifiers = $modifiers;
        return $this;
    }

    /**
     * @param string $modifier
     * @return $this
     */
    public function addModifier(string $modifier): Day
    {
        if (!in_array($modifier, $this->modifiers)) {
            $this->modifiers[] = $modifier;
        }

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param DateTimeInterface $date
     * @return Day
     */
    public function setDate(DateTimeInterface $date): Day
    {
        $this->date = $date;
        return $this;
    }
}
