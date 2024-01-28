<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class IngredientController extends AbstractController
{

    public function __construct(
        private IngredientRepository $ingredientRepository
    ) {
    }

    #[Route('/api/ingredients/{id}/recipes', name: 'get_recipes_by_ingredient', methods: ['GET'])]
    public function __invoke(int $id): Response
    {
        $ingredient = $this->ingredientRepository->find($id);
        if (!$ingredient) return $this->json(['message' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);

        $compositions = $ingredient->getCompositions();
        $recipes = [];

        foreach ($compositions as $composition)
            if (!in_array($composition->getRecipe(), $recipes, true)) $recipes[] = $composition->getRecipe();

        return $this->json($recipes, Response::HTTP_OK, [], ['groups' => ['recipe:read']]);
    }
}
