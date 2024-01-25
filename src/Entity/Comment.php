<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\CommentRepository;
use App\State\CommentProcessor;
use App\Security\Voter\CommentVoter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Delete(),
        new Post(
            processor: CommentProcessor::class,
            denormalizationContext: [
                'groups' => ['comment:create']
            ],
            security: 'is_granted(\'' . CommentVoter::CREATE . '\', object)'
        ),
        new Patch(),
        // Tous les commentaires d'un Utilisateur //
        new GetCollection(
            uriTemplate: '/user/{id}/comments',
            uriVariables: [
                'id' => new Link(
                    fromProperty: 'comments',
                    fromClass: User::class,
                )
            ]
        ),
        // Tous les commentaires lié à une Recette //
        new GetCollection(
            uriTemplate: '/recipe/{id}/comments',
            uriVariables: [
                'id' => new Link(
                    fromProperty: 'comments',
                    fromClass: Recipe::class,
                )
            ]
        )
    ]
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['comment:read', 'comment:create'])]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read', 'comment:create'])]
    private ?Recipe $recipe = null;

    #[ApiProperty(writable: false)]
    #[ORM\ManyToOne(inversedBy: 'comments', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

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
}
