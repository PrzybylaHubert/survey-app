<?php

namespace App\Entity;

use App\Repository\SurveyUserAnswerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SurveyUserAnswerRepository::class)]
class SurveyUserAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['surveyResults'])]
    private int $id;

    #[ORM\ManyToOne]
    #[Groups(['surveyResults'])]
    private ?SurveyOfferedAnswer $offeredAnswer = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SurveyQuestion $question = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['surveyResults'])]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'userAnswers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SurveyAssignment $surveyAssignment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOfferedAnswer(): ?SurveyOfferedAnswer
    {
        return $this->offeredAnswer;
    }

    public function setOfferedAnswer(?SurveyOfferedAnswer $offeredAnswer): static
    {
        $this->offeredAnswer = $offeredAnswer;

        return $this;
    }

    public function getQuestion(): ?SurveyQuestion
    {
        return $this->question;
    }

    public function setQuestion(?SurveyQuestion $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getSurveyAssignment(): ?SurveyAssignment
    {
        return $this->surveyAssignment;
    }

    public function setSurveyAssignment(?SurveyAssignment $surveyAssignment): static
    {
        $this->surveyAssignment = $surveyAssignment;

        return $this;
    }
}
