<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['surveyInfo'])]
    private int $id;

    #[ORM\Column(length: 180)]
    #[Groups(['surveyInfo'])]
    private string $email;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    /**
     * @var Collection<int, Survey>
     */
    #[ORM\OneToMany(targetEntity: Survey::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $surveys;

    /**
     * @var Collection<int, SurveyAssignment>
     */
    #[ORM\OneToMany(targetEntity: SurveyAssignment::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $surveyAssignments;

    public function __construct()
    {
        $this->surveys = new ArrayCollection();
        $this->surveyAssignments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Survey>
     */
    public function getSurveys(): Collection
    {
        return $this->surveys;
    }

    public function addSurvey(Survey $survey): static
    {
        if (!$this->surveys->contains($survey)) {
            $this->surveys->add($survey);
            $survey->setAuthor($this);
        }

        return $this;
    }

    public function removeSurvey(Survey $survey): static
    {
        if ($this->surveys->removeElement($survey)) {
            // set the owning side to null (unless already changed)
            if ($survey->getAuthor() === $this) {
                $survey->setAuthor(null);
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
            $surveyAssignment->setUser($this);
        }

        return $this;
    }

    public function removeSurveyAssignment(SurveyAssignment $surveyAssignment): static
    {
        if ($this->surveyAssignments->removeElement($surveyAssignment)) {
            // set the owning side to null (unless already changed)
            if ($surveyAssignment->getUser() === $this) {
                $surveyAssignment->setUser(null);
            }
        }

        return $this;
    }
}
