<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecetteRepository::class)]
#[ApiResource]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

<<<<<<< Updated upstream
    #[ORM\Column]
    private array $preparation = [];

    #[ORM\Column(length: 50)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $decription = null;
=======
    #[ORM\Column(length: 50)]
    private ?string $titre = null;

    #[ORM\Column]
    private array $preparation = [];

    #[ORM\Column(length: 255)]
    private ?string $description = null;
>>>>>>> Stashed changes

    #[ORM\Column]
    private array $detail = [];

<<<<<<< Updated upstream
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'recettes', fetch: 'EAGER')]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: Ingredient::class, inversedBy: 'recettes', fetch: 'EAGER')]
    private Collection $ingredients;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'recettes')]
    private ?Utilisateur $createur = null;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Favoris::class, orphanRemoval: true)]
    private Collection $favoris;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->favoris = new ArrayCollection();
=======
    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Favoris::class, orphanRemoval: true)]
    private Collection $favoris;

    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'recettes')]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: Ingredient::class, inversedBy: 'recettes')]
    private Collection $ingredients;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\ManyToOne(inversedBy: 'recettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $createur = null;

    public function __construct()
    {
        $this->favoris = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
>>>>>>> Stashed changes
    }

    public function getId(): ?int
    {
        return $this->id;
    }

<<<<<<< Updated upstream
    public function getPreparation(): array
    {
        return $this->preparation;
    }

    public function setPreparation(array $preparation): static
    {
        $this->preparation = $preparation;

        return $this;
    }

=======
>>>>>>> Stashed changes
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

<<<<<<< Updated upstream
    public function getDecription(): ?string
    {
        return $this->decription;
    }

    public function setDecription(string $decription): static
    {
        $this->decription = $decription;
=======
    public function getPreparation(): array
    {
        return $this->preparation;
    }

    public function setPreparation(array $preparation): static
    {
        $this->preparation = $preparation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
>>>>>>> Stashed changes

        return $this;
    }

    public function getDetail(): array
    {
        return $this->detail;
    }

    public function setDetail(array $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    /**
<<<<<<< Updated upstream
=======
     * @return Collection<int, Favoris>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Favoris $favori): static
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
            $favori->setRecette($this);
        }

        return $this;
    }

    public function removeFavori(Favoris $favori): static
    {
        if ($this->favoris->removeElement($favori)) {
            // set the owning side to null (unless already changed)
            if ($favori->getRecette() === $this) {
                $favori->setRecette(null);
            }
        }

        return $this;
    }

    /**
>>>>>>> Stashed changes
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): static
    {
        $this->ingredients->removeElement($ingredient);

        return $this;
    }

<<<<<<< Updated upstream
    public function getCreateur(): ?Utilisateur
    {
        return $this->createur;
    }

    public function setCreateur(?Utilisateur $createur): static
    {
        $this->createur = $createur;

        return $this;
    }

=======
>>>>>>> Stashed changes
    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setRecette($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getRecette() === $this) {
                $commentaire->setRecette(null);
            }
        }

        return $this;
    }

<<<<<<< Updated upstream
    /**
     * @return Collection<int, Favoris>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Favoris $favori): static
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
            $favori->setRecette($this);
        }

        return $this;
    }

    public function removeFavori(Favoris $favori): static
    {
        if ($this->favoris->removeElement($favori)) {
            // set the owning side to null (unless already changed)
            if ($favori->getRecette() === $this) {
                $favori->setRecette(null);
            }
        }
=======
    public function getCreateur(): ?Utilisateur
    {
        return $this->createur;
    }

    public function setCreateur(?Utilisateur $createur): static
    {
        $this->createur = $createur;
>>>>>>> Stashed changes

        return $this;
    }
}
