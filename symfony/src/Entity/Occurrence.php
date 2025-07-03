<?php

namespace App\Entity;

use App\Repository\OccurrenceRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ICal\Event as IcalEvent;

#[ORM\Entity(repositoryClass: OccurrenceRepository::class)]
class Occurrence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private $start;

    #[ORM\Column(type: 'datetime_immutable')]
    private $end;

    #[ORM\ManyToOne(inversedBy: 'occurrences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

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
     * Is this an all dat event?
     *
     * @return bool
     */
    public function isAllDay(): bool
    {
        return $this->start->diff($this->end)->format('%d') === '1';
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
