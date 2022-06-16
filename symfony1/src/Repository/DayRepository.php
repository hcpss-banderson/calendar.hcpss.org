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
    private $days;

    /**
     * @return Day[]
     */
    public function getDays(): array
    {
        ksort($this->days);
        return $this->days;
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

    /**
     * @param DateTimeInterface $start
     * @param DateTimeInterface $end
     * @return DayRepository
     */
    public function fillDates(DateTimeInterface $start, DateTimeInterface $end): DayRepository
    {
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
        foreach ($period as $date) {
            if (!array_key_exists($date->format('Y-m-d'), $this->days)) {
                $this->addDay(new Day($date));
            }
        }

        return $this;
    }
}
