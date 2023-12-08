<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ApiResource]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

<<<<<<< Updated upstream
    #[ORM\Column(length: 100)]
    private ?string $password = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column(length: 100)]
=======
    #[ORM\Column(length: 50)]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $statusProfil = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column(length: 50)]
>>>>>>> Stashed changes
    private ?string $mail = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $role = [];

<<<<<<< Updated upstream
    #[ORM\OneToMany(mappedBy: 'createur', targetEntity: Recette::class)]
    private Collection $recettes;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Favoris::class, orphanRemoval: true)]
    private Collection $favoris;

    public function __construct()
    {
        $this->recettes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->favoris = new ArrayCollection();
=======
    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Favoris::class, orphanRemoval: true)]
    private Collection $favoris;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'createur', targetEntity: Recette::class, orphanRemoval: true)]
    private Collection $recettes;

    public function __construct()
    {
        $this->favoris = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->recettes = new ArrayCollection();
>>>>>>> Stashed changes
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

<<<<<<< Updated upstream
    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;
=======
    public function getStatusProfil(): ?string
    {
        return $this->statusProfil;
    }

    public function setStatusProfil(string $statusProfil): static
    {
        $this->statusProfil = $statusProfil;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;
>>>>>>> Stashed changes

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getRole(): array
    {
        return $this->role;
    }

    public function setRole(array $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
<<<<<<< Updated upstream
     * @return Collection<int, Recette>
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recette $recette): static
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes->add($recette);
            $recette->setCreateur($this);
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
            $favori->setAuteur($this);
>>>>>>> Stashed changes
        }

        return $this;
    }

<<<<<<< Updated upstream
    public function removeRecette(Recette $recette): static
    {
        if ($this->recettes->removeElement($recette)) {
            // set the owning side to null (unless already changed)
            if ($recette->getCreateur() === $this) {
                $recette->setCreateur(null);
=======
    public function removeFavori(Favoris $favori): static
    {
        if ($this->favoris->removeElement($favori)) {
            // set the owning side to null (unless already changed)
            if ($favori->getAuteur() === $this) {
                $favori->setAuteur(null);
>>>>>>> Stashed changes
            }
        }

        return $this;
    }

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
            $commentaire->setAuteur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getAuteur() === $this) {
                $commentaire->setAuteur(null);
            }
        }

        return $this;
    }

    /**
<<<<<<< Updated upstream
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
            $favori->setAuteur($this);
=======
     * @return Collection<int, Recette>
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recette $recette): static
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes->add($recette);
            $recette->setCreateur($this);
>>>>>>> Stashed changes
        }

        return $this;
    }

<<<<<<< Updated upstream
    public function removeFavori(Favoris $favori): static
    {
        if ($this->favoris->removeElement($favori)) {
            // set the owning side to null (unless already changed)
            if ($favori->getAuteur() === $this) {
                $favori->setAuteur(null);
=======
    public function removeRecette(Recette $recette): static
    {
        if ($this->recettes->removeElement($recette)) {
            // set the owning side to null (unless already changed)
            if ($recette->getCreateur() === $this) {
                $recette->setCreateur(null);
>>>>>>> Stashed changes
            }
        }

        return $this;
    }
}
