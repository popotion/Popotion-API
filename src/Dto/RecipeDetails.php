<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

class RecipeDetails
{
    #[Assert\NotBlank(message: "La difficulté ne peut pas être vide")]
    #[Assert\NotNull(message: "La difficulté ne peut pas être null")]
    #[Assert\Range(min: 0, max: 5, notInRangeMessage: "La difficulté doit être comprise entre 1 et 5")]
    #[Groups(['recipe:create', 'recipe:update', 'recipe:read'])]
    public ?int $difficulty = null;

    #[Assert\NotBlank(message: "Le temps de préparation ne peut pas être vide")]
    #[Assert\NotNull(message: "Le temps de préparation ne peut pas être null")]
    #[Assert\Range(min: 0, max: 180, notInRangeMessage: "Le temps de préparation doit être compris entre 0 et 120 minutes")]
    #[Groups(['recipe:create', 'recipe:update', 'recipe:read'])]
    public ?int $preparationTime = null;

    #[Assert\NotBlank(message: "Le nombre de personnes ne peut pas être vide")]
    #[Assert\NotNull(message: "Le nombre de personnes ne peut pas être null")]
    #[Assert\Range(min: 0, max: 10, notInRangeMessage: "Le nombre de personnes doit être compris entre 1 et 10")]
    #[Groups(['recipe:create', 'recipe:update', 'recipe:read'])]
    public ?int $nbPersons = null;

    public function toArray(): array
    {
        return [
            "Difficulté: " . $this->difficulty . "/5",
            "Temps de préparation: " . $this->preparationTime . " min",
            "Pour: " . $this->nbPersons . " personnes"
        ];
    }
}
