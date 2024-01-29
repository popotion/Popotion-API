<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use App\Repository\FavoriteRepository;
use App\State\FavoriteProcessor;
use App\Security\Voter\FavoriteVoter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
#[ApiResource(
    operations: [
        new Delete(
            security: 'is_granted(\'' . FavoriteVoter::DELETE . '\', object)'
        ),
        new Post(
            processor: FavoriteProcessor::class,
            security: 'is_granted(\'' . FavoriteVoter::CREATE . '\', object)',
            denormalizationContext: [
                'groups' => ['favorite:create']
            ]
        ),
    ],
    normalizationContext: [
        'groups' => ['favorite:read']
    ]
)]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['favorite:read', 'recipe:read'])]
    #[ApiProperty(writable: false)]
    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Groups(['favorite:create', 'favorite:read', 'user:read'])]
    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "La recette ne peut pas être vide")]
    #[Assert\NotNull(message: "La recette ne peut pas être null")]
    private ?Recipe $recipe = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }
}
