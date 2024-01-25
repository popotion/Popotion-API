<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CompositionData
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 3, max: 40, minMessage: "Le nom de l'ingrédient doit contenir au moins 3 caractères", maxMessage: "Le nom de l'ingrédient doit contenir au maximum 40 caractères")]
    public string $ingredientName;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 999, notInRangeMessage: "La quantité doit être comprise entre 0 et 999")]
    public int $quantity;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 1, max: 10, minMessage: "L'unité doit contenir au moins 1 caractère", maxMessage: "L'unité doit contenir au maximum 10 caractères")]
    public string $unit;
}
