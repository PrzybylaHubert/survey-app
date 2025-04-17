<?php

namespace App\Entity;

use App\Repository\SurveyAssignmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SurveyAssignmentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SurveyAssignment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'surveyAssignments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Survey $survey = null;

    #[ORM\ManyToOne(inversedBy: 'surveyAssignments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column]
    private \DateTimeImmutable $dateStart;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateFinish = null;

    /**
     * @var Collection<int, SurveyUserAnswer>
     */
    #[ORM\OneToMany(targetEntity: SurveyUserAnswer::class, mappedBy: 'surveyAssignment', orphanRemoval: true)]
    private Collection $userAnswers;

    public function __construct()
    {
        $this->userAnswers = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    public function setSurvey(?Survey $survey): static
    {
        $this->survey = $survey;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDateStart(): \DateTimeImmutable
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeImmutable $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    #[ORM\PrePersist]
    public function setDateStartAutomatically(): void
    {
        $this->dateStart = new \DateTimeImmutable();
    }

    public function getDateFinish(): ?\DateTimeImmutable
    {
        return $this->dateFinish;
    }

    public function setDateFinish(?\DateTimeImmutable $dateFinish): static
    {
        $this->dateFinish = $dateFinish;

        return $this;
    }

    /**
     * @return Collection<int, SurveyUserAnswer>
     */
    public function getUserAnswers(): Collection
    {
        return $this->userAnswers;
    }

    public function addUserAnswer(SurveyUserAnswer $userAnswer): static
    {
        if (!$this->userAnswers->contains($userAnswer)) {
            $this->userAnswers->add($userAnswer);
            $userAnswer->setSurveyAssignment($this);
        }

        return $this;
    }

    public function removeUserAnswer(SurveyUserAnswer $userAnswer): static
    {
        if ($this->userAnswers->removeElement($userAnswer)) {
            // set the owning side to null (unless already changed)
            if ($userAnswer->getSurveyAssignment() === $this) {
                $userAnswer->setSurveyAssignment(null);
            }
        }

        return $this;
    }
}
