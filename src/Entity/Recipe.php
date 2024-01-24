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
use App\Repository\RecipeRepository;
use App\State\RecipeProcessor;
use App\Security\Voter\RecipeVoter;
use App\Dto\CompositionData;
use App\Dto\RecipeDetails;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Patch(
            denormalizationContext: [
                'groups' => ['recipe:update']
            ],
            security: 'is_granted(\'' . RecipeVoter::EDIT . '\', object)'
        ),
        new Delete(
            security: 'is_granted(\'' . RecipeVoter::DELETE . '\', object)'
        ),
        new Post(
            processor: RecipeProcessor::class,
            validationContext: [
                'groups' => ['Default', 'recipe:create']
            ],
            security: 'is_granted(\'' . RecipeVoter::CREATE . '\', object)'
        ),
        new GetCollection(),
        // Toutes les Recettes d'un Utilisateur) //
        new GetCollection(
            uriTemplate: '/user/{id}/recipe',
            uriVariables: [
                'id' => new Link(
                    fromProperty: 'recipes',
                    fromClass: User::class,
                )
            ]
        ),
        // Toutes les Recettes contenues dans une Catégorie) //
        new GetCollection(
            uriTemplate: '/categories/{id}/recipes',
            uriVariables: [
                'id' => new Link(
                    fromProperty: 'recipes',
                    fromClass: Category::class,
                )
            ]
        ),
        // TODO Toutes les Recettes ou sont contenues un Ingrédient)
    ],
    normalizationContext: [
        'groups' => ['recipe:read']
    ],
    order: ['datePublication' => 'DESC']
)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['recipe:read'])]
    private ?int $id = null;

    #[Assert\NotNull(groups: ['recipe:create'])]
    #[Assert\NotBlank(groups: ['recipe:create'])]
    #[Assert\Length(min: 3, max: 40, minMessage: 'Il faut au moins 3 caractères', maxMessage: 'Il faut au plus 40 caractères', groups: ['recipe:create'])]
    #[ORM\Column(length: 255)]
    #[Groups(['recipe:read', 'recipe:create', 'recipe:update'])]
    private ?string $title = null;

    #[Assert\NotNull(groups: ['recipe:create'])]
    #[Assert\NotBlank(groups: ['recipe:create'])]
    #[Assert\Length(min: 3, max: 420, minMessage: 'Il faut au moins 3 caractères', maxMessage: 'Il faut au plus 420 caractères', groups: ['recipe:create'])]
    #[Groups(['recipe:read', 'recipe:create', 'recipe:update'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Assert\Valid]
    #[ApiProperty(writable: true, readable: false)]
    #[Groups(['recipe:create', 'recipe:update'])]
    private RecipeDetails $recipeDetails;

    #[Groups(['recipe:read'])]
    #[ApiProperty(writable: false)]
    #[ORM\Column]
    private array $details = [];

    #[Assert\NotNull(groups: ['recipe:create'])]
    #[Assert\NotBlank(groups: ['recipe:create'])]
    #[ORM\Column]
    #[Groups(['recipe:read', 'recipe:create', 'recipe:update'])]
    private array $preparation = [];

    #[ORM\ManyToOne(inversedBy: 'recipes', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ApiProperty(writable: false)]
    #[Groups(['recipe:read'])]
    private ?User $author = null;

    /**
     * @var string[]
     */
    #[ApiProperty(writable: true, readable: false)]
    #[Assert\NotNull(groups: ['recipe:create'])]
    #[Assert\NotBlank(groups: ['recipe:create'])]
    #[Assert\Count(min: 1, minMessage: 'Il faut au moins une catégorie', groups: ['recipe:create'])]
    #[Groups(['recipe:create', 'recipe:update'])]
    private array $categoryNames = [];

    #[Groups(['recipe:read'])]
    #[ApiProperty(writable: false)]
    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'recipes', cascade: ['persist'], fetch: 'EAGER')]
    private Collection $categories;

    #[Groups(['recipe:read'])]
    #[ApiProperty(writable: false)]
    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Comment::class, orphanRemoval: true, fetch: 'EAGER')]
    private Collection $comments;

    #[Groups(['recipe:read'])]
    #[ApiProperty(writable: false)]
    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Favorite::class, orphanRemoval: true, fetch: 'EAGER')]
    private Collection $favorites;

    /**
     * @var CompositionData[]
     */
    #[ApiProperty(writable: true, readable: false)]
    #[Assert\Valid]
    #[Groups(['recipe:create', 'recipe:update'])]
    private array $compositionsData = [];

    #[Groups(['recipe:read'])]
    #[ApiProperty(writable: false)]
    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Composition::class, orphanRemoval: true, cascade: ['persist'], fetch: 'EAGER')]
    private Collection $compositions;

    #[Groups(['recipe:read'])]
    #[ApiProperty(writable: false)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datePublication = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->compositions = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersistDatePublication(): void
    {
        $this->datePublication = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRecipeDetails(): RecipeDetails
    {
        return $this->recipeDetails;
    }

    public function setRecipeDetails(RecipeDetails $recipeDetails): static
    {
        $this->recipeDetails = $recipeDetails;

        return $this;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getPreparation(): array
    {
        return $this->preparation;
    }

    public function setPreparation(array $preparation): static
    {
        $this->preparation = $preparation;

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

    public function getCategoryNames(): array
    {
        return $this->categoryNames;
    }

    public function setCategoryNames(array $categoryNames): static
    {
        $this->categoryNames = $categoryNames;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addRecipe($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeRecipe($this);
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
            $comment->setRecipe($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getRecipe() === $this) {
                $comment->setRecipe(null);
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
            $favorite->setRecipe($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getRecipe() === $this) {
                $favorite->setRecipe(null);
            }
        }

        return $this;
    }

    public function getCompositionsData(): array
    {
        return $this->compositionsData;
    }

    public function setCompositionsData(array $compositionsData): self
    {
        $this->compositionsData = $compositionsData;
        return $this;
    }

    /**
     * @return Collection<int, Composition>
     */
    public function getCompositions(): Collection
    {
        return $this->compositions;
    }

    public function addComposition(Composition $composition): static
    {
        if (!$this->compositions->contains($composition)) {
            $this->compositions->add($composition);
            $composition->setRecipe($this);
        }

        return $this;
    }

    public function removeComposition(Composition $composition): static
    {
        if ($this->compositions->removeElement($composition)) {
            // set the owning side to null (unless already changed)
            if ($composition->getRecipe() === $this) {
                $composition->setRecipe(null);
            }
        }

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
}
