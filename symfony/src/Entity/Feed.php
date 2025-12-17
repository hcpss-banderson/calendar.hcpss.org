<?php

namespace App\Entity;

use App\Repository\FeedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedRepository::class)]
class Feed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $source = null;

    #[ORM\ManyToMany(targetEntity: Calendar::class, inversedBy: 'feeds')]
    private Collection $calendars;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ics = null;

    public function __construct()
    {
        $this->calendars = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return Collection<int, Calendar>
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): static
    {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars->add($calendar);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): static
    {
        $this->calendars->removeElement($calendar);

        return $this;
    }

    public function getIcs(): ?string
    {
        return $this->ics;
    }

    public function setIcs(?string $ics): static
    {
        $this->ics = $ics;

        return $this;
    }
}
