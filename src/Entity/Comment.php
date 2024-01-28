<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\CommentRepository;
use App\State\CommentProcessor;
use App\Security\Voter\CommentVoter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    operations: [
        new Delete(
            security: 'is_granted(\'' . CommentVoter::DELETE . '\', object)'
        ),
        new Post(
            processor: CommentProcessor::class,
            denormalizationContext: [
                'groups' => ['comment:create']
            ],
            security: 'is_granted(\'' . CommentVoter::CREATE . '\', object)'
        ),
        new Patch(
            security: 'is_granted(\'' . CommentVoter::EDIT . '\', object)',
            denormalizationContext: [
                'groups' => ['comment:update']
            ]
        ),
    ],
    normalizationContext: [
        'groups' => ['comment:read']
    ],
    order: ['datePublication' => 'DESC']
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['comment:read', 'comment:create', 'comment:update', 'recipe:read', 'user:read'])]
    #[Assert\NotBlank(message: "Le message ne peut pas être vide")]
    #[Assert\NotNull(message: "Le message ne peut pas être null")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le message doit contenir au moins 3 caractères", maxMessage: "Le message doit contenir au maximum 255 caractères")]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:create', 'comment:read', 'user:read'])]
    #[Assert\NotBlank(message: "La recette ne peut pas être vide")]
    #[Assert\NotNull(message: "La recette ne peut pas être null")]
    private ?Recipe $recipe = null;

    #[ApiProperty(writable: false)]
    #[ORM\ManyToOne(inversedBy: 'comments', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read', 'recipe:read'])]
    private ?User $author = null;

    #[Groups(['comment:read', 'recipe:read', 'user:read'])]
    #[ApiProperty(writable: false)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datePublication = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

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

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    #[ORM\PrePersist]
    public function prePersistDatePublication(): void
    {
        $this->datePublication = new \DateTime();
    }
}
