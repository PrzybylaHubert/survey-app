<?php

namespace App\Entity;

use App\Repository\SurveyOfferedAnswerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SurveyOfferedAnswerRepository::class)]
class SurveyOfferedAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'offeredAnswers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SurveyQuestion $question = null;

    #[ORM\Column(length: 511)]
    private string $offeredAnswer;

    public function getId(): int
    {
        return $this->id;
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

    public function getOfferedAnswer(): string
    {
        return $this->offeredAnswer;
    }

    public function setOfferedAnswer(string $offeredAnswer): static
    {
        $this->offeredAnswer = $offeredAnswer;

        return $this;
    }
}
