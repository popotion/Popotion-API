<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
#[ApiResource(
    operations: [
        new Delete(),
        new Post(),
        // Tous les Favoris d'un Utilisateur) //
        new GetCollection(
            uriTemplate: '/user/{id}/favoris',
            uriVariables: [
                'id' => new Link(
                    fromProperty: 'comments',
                    fromClass: User::class,
                )
            ]),
        // Tous les Favoris d'une Recette) //
        new GetCollection(
            uriTemplate: '/recipe/{id}/favoris',
            uriVariables: [
                'id' => new Link(
                    fromProperty: 'comments',
                    fromClass: Recipe::class,
                )
            ]),
    ],
)]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
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
