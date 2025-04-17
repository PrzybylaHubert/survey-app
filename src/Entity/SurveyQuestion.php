<?php

namespace App\Entity;

use App\Enum\QuestionType;
use App\Repository\SurveyQuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SurveyQuestionRepository::class)]
class SurveyQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['surveyFull'])]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SurveySection $section = null;

    #[ORM\Column(enumType: QuestionType::class)]
    #[Groups(['surveyFull'])]
    private QuestionType $questionType;

    #[ORM\Column(length: 511)]
    #[Groups(['surveyFull'])]
    private string $question;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['surveyFull'])]
    private ?int $limiter = null;

    /**
     * @var Collection<int, SurveyOfferedAnswer>
     */
    #[ORM\OneToMany(targetEntity: SurveyOfferedAnswer::class, mappedBy: 'question', orphanRemoval: true)]
    #[Groups(['surveyFull'])]
    private Collection $offeredAnswers;

    public function __construct()
    {
        $this->offeredAnswers = new ArrayCollection();
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getSection(): ?SurveySection
    {
        return $this->section;
    }

    public function setSection(?SurveySection $section): static
    {
        $this->section = $section;

        return $this;
    }

    public function getQuestionType(): QuestionType
    {
        return $this->questionType;
    }

    public function setQuestionType(QuestionType $questionType): static
    {
        $this->questionType = $questionType;

        return $this;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getLimiter(): ?int
    {
        return $this->limiter;
    }

    public function setLimiter(?int $limiter): static
    {
        $this->limiter = $limiter;

        return $this;
    }

    /**
     * @return Collection<int, SurveyOfferedAnswer>
     */
    public function getOfferedAnswers(): Collection
    {
        return $this->offeredAnswers;
    }

    public function addOfferedAnswer(SurveyOfferedAnswer $offeredAnswer): static
    {
        if (!$this->offeredAnswers->contains($offeredAnswer)) {
            $this->offeredAnswers->add($offeredAnswer);
            $offeredAnswer->setQuestion($this);
        }

        return $this;
    }

    public function removeOfferedAnswer(SurveyOfferedAnswer $offeredAnswer): static
    {
        if ($this->offeredAnswers->removeElement($offeredAnswer)) {
            // set the owning side to null (unless already changed)
            if ($offeredAnswer->getQuestion() === $this) {
                $offeredAnswer->setQuestion(null);
            }
        }

        return $this;
    }
}
