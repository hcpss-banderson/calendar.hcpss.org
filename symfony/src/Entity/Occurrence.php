<?php

namespace App\Entity;

use App\Repository\OccurrenceRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use ICal\Event as IcalEvent;

#[ORM\Entity(repositoryClass: OccurrenceRepository::class)]
class Occurrence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'datetime_immutable')]
    private $start;

    #[ORM\Column(type: 'datetime_immutable')]
    private $end;

    #[ORM\ManyToOne(targetEntity: Calendar::class, inversedBy: 'occurrences')]
    #[ORM\JoinColumn(nullable: false)]
    private $calendar;

    /**
     * @param string $title
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     * @param string|null $description
     */
    public function __construct(string $title, DateTimeImmutable $start, DateTimeImmutable $end, string $description = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @param IcalEvent $ical
     * @return static
     * @throws \Exception
     */
    public static function fromIcal(IcalEvent $ical): self
    {
        return new self(
            $ical->summary,
            new DateTimeImmutable($ical->dtstart_tz),
            new DateTimeImmutable($ical->dtend_tz),
            $ical->description,
        );
    }

    /**
     * Get the id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description.
     *
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get start.
     *
     * @return DateTimeImmutable|null
     */
    public function getStart(): ?\DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * Set start.
     *
     * @param DateTimeImmutable $start
     * @return $this
     */
    public function setStart(\DateTimeImmutable $start): self
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get end.
     *
     * @return DateTimeImmutable|null
     */
    public function getEnd(): ?\DateTimeImmutable
    {
        return $this->end;
    }

    /**
     * Set end.
     *
     * @param DateTimeImmutable $end
     * @return $this
     */
    public function setEnd(\DateTimeImmutable $end): self
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get calendar.
     *
     * @return Calendar|null
     */
    public function getCalendar(): ?Calendar
    {
        return $this->calendar;
    }

    /**
     * Set the calendar.
     *
     * @param Calendar|null $calendar
     * @return $this
     */
    public function setCalendar(?Calendar $calendar): self
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * Is this an all dat event?
     *
     * @return bool
     */
    public function isAllDay(): bool
    {
        return $this->start->diff($this->end)->format('%d') === '1';
    }
}
