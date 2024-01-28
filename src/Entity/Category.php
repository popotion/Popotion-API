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
use App\Repository\CategoryRepository;
use App\Security\Voter\CategoryVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Delete(
            security: 'is_granted(\'' . CategoryVoter::DELETE . '\', object)'
        ),
        new Post(),
        new Patch(
            security: 'is_granted(\'' . CategoryVoter::EDIT . '\', object)'
        ),
        // Toutes les catégories d'une Recette //
        new GetCollection(
            uriTemplate: '/recipe/{id}/categories',
            uriVariables: [
                'id' => new Link(
                    fromProperty: 'categories',
                    toProperty: 'recipes',
                    fromClass: Recipe::class,
                    toClass: Category::class
                )
            ]
        )
    ],
    normalizationContext: ['groups' => ['category:read']],
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recipe:read', 'user:read', 'category:read'])]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide")]
    #[Assert\NotNull(message: "Le nom ne peut pas être null")]
    #[Assert\Length(min: 3, max: 55, minMessage: "Le nom doit contenir au moins 3 caractères", maxMessage: "Le nom ne peut pas contenir plus de 55 caractères")]
    private ?string $name = null;

    #[Groups(['category:read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ApiProperty(writable: false)]
    #[Groups(['category:read'])]
    #[ORM\ManyToMany(targetEntity: Recipe::class, inversedBy: 'categories', cascade: ['persist'], fetch: 'EAGER')]
    private Collection $recipes;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): static
    {
        $this->recipes->removeElement($recipe);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
