<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use App\State\UserProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Delete(
            security: 'is_granted(\'' . UserVoter::EDIT . '\', object)'
        ),
        new GetCollection(),
        new Patch(
            processor: UserProcessor::class,
            denormalizationContext: [
                'groups' => ['user:update']
            ],
            security: 'is_granted(\'' . UserVoter::EDIT . '\', object)'
        ),
        new Post(
            processor: UserProcessor::class,
            denormalizationContext: [
                'groups' => ['user:create']
            ],
            validationContext: [
                'groups' => ['Default', 'user:create']
            ],
        )
    ],
    normalizationContext: [
        'groups' => ['user:read']
    ],
)]
#[UniqueEntity(fields: ['login'], message: 'Ce login est déjà utilisé.')]
#[UniqueEntity(fields: ['mailAdress'], message: 'Cette adresse mail est déjà utilisée.')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(['user:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['user:read', 'user:update', 'user:create'])]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 4, max: 200, minMessage: 'Il faut au moins 4 caractères', maxMessage: 'Il faut moins de 200 caractères')]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $login = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['user:create', 'user:update'])]
    #[Assert\NotBlank(groups: ['user:create'])]
    #[Assert\NotNull(groups: ['user:create'])]
    #[Assert\Length(min: 8, max: 30, minMessage: 'Il faut au moins 8 caractères', maxMessage: 'Il faut moins de 30 caractères', groups: ['user:create'])]
    #[Assert\Regex(pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,30}$/', message: 'Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre.', groups: ['user:create'])]
    private ?string $plainPassword = null;

    #[UserPassword(message: 'Le mot de passe actuel est incorrect.', groups: ['user:update'])]
    private ?string $currentPlainPassword = null;

    #[Groups(['user:read', 'user:update'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $status = null;

    #[Groups(['user:update', 'user:create'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[Groups(['user:read', 'user:update', 'user:create'])]
    #[Assert\NotBlank(groups: ['user:create'])]
    #[Assert\NotNull(groups: ['user:create'])]
    #[Assert\Email(message: 'L\'adresse mail n\'est pas valide.', groups: ['user:create', 'user:update'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $mailAdress = null;

    #[Groups(['user:read', 'user:update'])]
    #[ApiProperty(readable: true, writable: false)]
    #[ORM\Column(options: ["default" => false])]
    private ?bool $premium = false;

    #[Groups(['user:read'])]
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Recipe::class, orphanRemoval: true)]
    private Collection $recipes;

    #[Groups(['user:read'])]
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[Groups(['user:read'])]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Favorite::class, orphanRemoval: true)]
    private Collection $favorites;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
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
        $this->plainPassword = null;
        $this->currentPlainPassword = null;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getMailAdress(): ?string
    {
        return $this->mailAdress;
    }

    public function setMailAdress(string $mailAdress): static
    {
        $this->mailAdress = $mailAdress;

        return $this;
    }

    public function isPremium(): ?bool
    {
        return $this->premium;
    }

    public function setPremium(bool $premium): static
    {
        $this->premium = $premium;

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): static
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes->add($recipe);
            $recipe->setAuthor($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): static
    {
        if ($this->recipes->removeElement($recipe)) {
            // set the owning side to null (unless already changed)
            if ($recipe->getAuthor() === $this) {
                $recipe->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setUser($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getUser() === $this) {
                $favorite->setUser(null);
            }
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getCurrentPlainPassword(): ?string
    {
        return $this->currentPlainPassword;
    }

    public function setCurrentPlainPassword(string $currentPlainPassword): static
    {
        $this->currentPlainPassword = $currentPlainPassword;

        return $this;
    }
}
