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

    #[ORM\Column(length: 50)]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $statusProfil = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column(length: 50)]
    private ?string $mail = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $role = [];

    #[ORM\OneToMany(mappedBy: 'createur', targetEntity: Recette::class, orphanRemoval: true)]
    private Collection $recettes;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Favoris::class, orphanRemoval: true)]
    private Collection $favoris;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $yes;

    public function __construct()
    {
        $this->recettes = new ArrayCollection();
        $this->favoris = new ArrayCollection();
        $this->yes = new ArrayCollection();
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
        }

        return $this;
    }

    public function removeRecette(Recette $recette): static
    {
        if ($this->recettes->removeElement($recette)) {
            // set the owning side to null (unless already changed)
            if ($recette->getCreateur() === $this) {
                $recette->setCreateur(null);
            }
        }

        return $this;
    }

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
            $favori->setAuteur($this);
        }

        return $this;
    }

    public function removeFavori(Favoris $favori): static
    {
        if ($this->favoris->removeElement($favori)) {
            // set the owning side to null (unless already changed)
            if ($favori->getAuteur() === $this) {
                $favori->setAuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getYes(): Collection
    {
        return $this->yes;
    }

    public function addYe(Commentaire $ye): static
    {
        if (!$this->yes->contains($ye)) {
            $this->yes->add($ye);
            $ye->setAuteur($this);
        }

        return $this;
    }

    public function removeYe(Commentaire $ye): static
    {
        if ($this->yes->removeElement($ye)) {
            // set the owning side to null (unless already changed)
            if ($ye->getAuteur() === $this) {
                $ye->setAuteur(null);
            }
        }

        return $this;
    }
}
