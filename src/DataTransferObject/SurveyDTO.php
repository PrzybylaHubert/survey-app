<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

class SurveyDTO
{
    #[Assert\Length(min: 3, max: 255)]
    #[Assert\NotBlank]
    private string $name;

    #[Assert\NotNull]
    #[Assert\Type("bool")]
    private bool $isActive;

    #[Assert\Length(max: 1023)]
    private ?string $description = null;

    /**
     * @var SurveySectionDTO[] $sections
     */
    #[Assert\Valid(groups: ['create'])]
    #[Assert\Type('array', groups: ['create'])]
    #[Assert\NotNull(message: "Sections must be provided.", groups: ['create'])]
    #[Assert\Count(min: 1, minMessage: "You must provide at least one question.", groups: ['create'])]
    private array $sections;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return SurveySectionDTO[]
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * @param SurveySectionDTO[] $sections
     */
    public function setSections(array $sections): void
    {
        $this->sections = $sections;
    }
}
