<?php

namespace App\Repository;

use App\Model\Day;
use DateInterval;
use DatePeriod;
use DateTimeInterface;

class DayRepository {

    /**
     * @var Day[]
     */
    private $days = [];

    /**
     * @return Day[]
     */
    public function getDays(): array
    {
        ksort($this->days);
        return $this->days;
    }

    /**
     * @return self[]
     * @throws \DateMalformedPeriodStringException
     */
    public function getMonths(): array
    {
        ksort($this->days);
        $end = end($this->days)->getDate();
        $start = reset($this->days)->getDate();
        $interval = new DateInterval('P1M');
        $period = new DatePeriod($start, $interval, $end);

        $months = [];
        foreach ($period as $date) {
            $repo = new self();
            foreach ($this->getDays() as $day) {
                if ($date->format('m') === $day->getDate()->format('m')) {
                    $repo->addDay($day);
                }
            }
            $months[] = $repo;
        }
        return $months;
    }

    /**
     * @param Day[] $days
     * @return DayRepository
     */
    public function setDays(array $days): DayRepository
    {
        $this->days = $days;
        return $this;
    }

    /**
     * @param Day $day
     * @return $this
     */
    public function addDay(Day $day): DayRepository
    {
        $this->days[$day->getDate()->format('Y-m-d')] = $day;
        return $this;
    }

    /**
     * @param string|DateTimeInterface $date
     * @return Day|null
     */
    public function getDay(string|DateTimeInterface $date): ?Day
    {
        $key = $date instanceof DateTimeInterface ? $date->format('Y-m-d') : $date;
        return $this->days[$key] ?? null;
    }

    public function padMonth(): DayRepository
    {
        $month = new self();
        $days  = $this->getDays();
        $last  = end($days)->getDate();
        $first = reset($days)->getDate();

        $startDate = $first->format('w') == '0'
            ? $first
            : $first->modify('last Sunday');

        $endDate = $last->format('w') == '6'
            ? $last
            : $last->modify('next Sunday');

        return $month
            ->setDays($days)
            ->fillDates($startDate, $endDate, [Day::DISABLED_DAY]);
    }

    /**
     * @param DateTimeInterface $start
     * @param DateTimeInterface $end
     * @param string[] $modifiers
     *   The modifiers to add to filled days.
     * @return DayRepository
     */
    public function fillDates(DateTimeInterface $start, DateTimeInterface $end, ?array $modifiers = []): DayRepository
    {
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
        foreach ($period as $date) {
            if (!array_key_exists($date->format('Y-m-d'), $this->days)) {
                $day = new Day($date);
                $day->setModifiers($modifiers);
                $this->addDay($day);
            }
        }
        ksort($this->days);
        return $this;
    }
}
