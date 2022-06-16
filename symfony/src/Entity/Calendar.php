<?php

namespace App\Entity;

use App\Repository\CalendarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'simple_array')]
    private $sources = [];

    #[ORM\Column(type: 'string', length: 50)]
    private $slug;

    #[ORM\OneToMany(mappedBy: 'calendar', targetEntity: Occurrence::class, orphanRemoval: true)]
    private $occurrences;

    #[ORM\Column(type: 'string', length: 100)]
    private $title;

    public function __construct()
    {
        $this->occurrences = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get sources.
     *
     * @return array|null
     */
    public function getSources(): ?array
    {
        return $this->sources;
    }

    /**
     * Set sources.
     *
     * @param array $sources
     * @return $this
     */
    public function setSources(array $sources): self
    {
        $this->sources = $sources;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get occurrences.
     *
     * @return Collection<int, Occurrence>
     */
    public function getOccurrences(): Collection
    {
        return $this->occurrences;
    }

    /**
     * Add an occurrence.
     *
     * @param Occurrence $occurrence
     * @return $this
     */
    public function addOccurrence(Occurrence $occurrence): self
    {
        if (!$this->occurrences->contains($occurrence)) {
            $this->occurrences[] = $occurrence;
            $occurrence->setCalendar($this);
        }

        return $this;
    }

    /**
     * Remove occurrence.
     *
     * @param Occurrence $occurrence
     * @return $this
     */
    public function removeOccurrence(Occurrence $occurrence): self
    {
        if ($this->occurrences->removeElement($occurrence)) {
            // set the owning side to null (unless already changed)
            if ($occurrence->getCalendar() === $this) {
                $occurrence->setCalendar(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
