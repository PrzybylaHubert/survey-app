<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SurveyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SurveyRepository::class)]
class Survey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['surveyInfo'])]
    private int $id;

    #[ORM\Column]
    #[Groups(['surveyInfo'])]
    private bool $is_active;

    #[ORM\Column(length: 255)]
    #[Groups(['surveyInfo'])]
    private string $name;

    #[ORM\ManyToOne(inversedBy: 'surveys')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['surveyInfo'])]
    private ?User $author = null;

    /**
     * @var Collection<int, SurveySection>
     */
    #[ORM\OneToMany(targetEntity: SurveySection::class, mappedBy: 'survey', orphanRemoval: true)]
    #[Groups(['surveyFull'])]
    private Collection $surveySections;

    /**
     * @var Collection<int, SurveyAssignment>
     */
    #[ORM\OneToMany(targetEntity: SurveyAssignment::class, mappedBy: 'survey', orphanRemoval: true)]
    private Collection $surveyAssignments;

    #[ORM\Column(length: 1023, nullable: true)]
    #[Groups(['surveyInfo'])]
    private ?string $description = null;

    public function __construct()
    {
        $this->surveySections = new ArrayCollection();
        $this->surveyAssignments = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, SurveySection>
     */
    public function getSurveySections(): Collection
    {
        return $this->surveySections;
    }

    public function addSurveySection(SurveySection $surveySection): static
    {
        if (!$this->surveySections->contains($surveySection)) {
            $this->surveySections->add($surveySection);
            $surveySection->setSurvey($this);
        }

        return $this;
    }

    public function removeSurveySection(SurveySection $surveySection): static
    {
        if ($this->surveySections->removeElement($surveySection)) {
            // set the owning side to null (unless already changed)
            if ($surveySection->getSurvey() === $this) {
                $surveySection->setSurvey(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SurveyAssignment>
     */
    public function getSurveyAssignments(): Collection
    {
        return $this->surveyAssignments;
    }

    public function addSurveyAssignment(SurveyAssignment $surveyAssignment): static
    {
        if (!$this->surveyAssignments->contains($surveyAssignment)) {
            $this->surveyAssignments->add($surveyAssignment);
            $surveyAssignment->setSurvey($this);
        }

        return $this;
    }

    public function removeSurveyAssignment(SurveyAssignment $surveyAssignment): static
    {
        if ($this->surveyAssignments->removeElement($surveyAssignment)) {
            // set the owning side to null (unless already changed)
            if ($surveyAssignment->getSurvey() === $this) {
                $surveyAssignment->setSurvey(null);
            }
        }

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
