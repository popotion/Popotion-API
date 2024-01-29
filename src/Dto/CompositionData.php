<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

class CompositionData
{
    #[Assert\NotBlank(message: "Le nom de l'ingrédient ne peut pas être vide")]
    #[Assert\NotNull(message: "Le nom de l'ingrédient ne peut pas être null")]
    #[Assert\Length(min: 3, max: 40, minMessage: "Le nom de l'ingrédient doit contenir au moins 3 caractères", maxMessage: "Le nom de l'ingrédient doit contenir au maximum 40 caractères")]
    #[Groups(['recipe:create', 'recipe:update', 'recipe:read'])]
    public ?string $ingredientName = null;

    #[Assert\NotBlank(message: "La quantité ne peut pas être vide")]
    #[Assert\NotNull(message: "La quantité ne peut pas être null")]
    #[Assert\Range(min: 0, max: 999, notInRangeMessage: "La quantité doit être comprise entre 0 et 999")]
    #[Groups(['recipe:create', 'recipe:update', 'recipe:read'])]
    public ?int $quantity = null;

    #[Assert\NotBlank(message: "L'unité ne peut pas être vide")]
    #[Assert\NotNull(message: "L'unité ne peut pas être null")]
    #[Assert\Length(min: 1, max: 10, minMessage: "L'unité doit contenir au moins 1 caractère", maxMessage: "L'unité doit contenir au maximum 10 caractères")]
    #[Groups(['recipe:create', 'recipe:update', 'recipe:read'])]
    public ?string $unit = null;
}
