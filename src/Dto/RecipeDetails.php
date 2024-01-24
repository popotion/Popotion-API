<?php

namespace App\Dto;

class RecipeDetails
{
    public int $difficulty;
    public int $preparationTime;
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
