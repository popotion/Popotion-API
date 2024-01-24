<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RecipeDetails
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 5, notInRangeMessage: "La difficulté doit être comprise entre 1 et 5")]
    public int $difficulty;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 180, notInRangeMessage: "Le temps de préparation doit être compris entre 0 et 120 minutes")]
    public int $preparationTime;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 10, notInRangeMessage: "Le nombre de personnes doit être compris entre 1 et 10")]
    public int $nbPersons;

    public function toArray(): array
    {
        return [
            "Difficulté: " . $this->difficulty . "/5",
            "Temps de préparation: " . $this->preparationTime . "min",
            "Pour: " . $this->nbPersons . " personnes"
        ];
    }
}
