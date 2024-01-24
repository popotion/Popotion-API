<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\RecipeDetails;
use App\Entity\Category;
use App\Entity\Composition;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Repository\CategoryRepository;
use App\Repository\IngredientRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RecipeProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] private ProcessorInterface $persistProcessor,
        private Security $security,
        private IngredientRepository $ingredientRepository,
        private CategoryRepository $categoryRepository
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->security->getUser();
        $this->setAuthor($data, $user);

        $this->createIngredientsAndSetCompositions($data, $data->getCompositionsData());

        $this->createCategories($data->getCategoryNames(), $data);

        $this->setRecipeDetails($data, $data->getRecipeDetails());

        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    public function setAuthor(Recipe $recipe, mixed $author): void
    {
        if ($author == null) return;
        $recipe->setAuthor($author);
    }

    public function createIngredientsAndSetCompositions(Recipe $recipe, array $compositionsData): void
    {
        foreach ($compositionsData as $compositionData) {
            $ingredient = $this->ingredientRepository->findOneByName($compositionData->ingredientName);
            if (!$ingredient) {
                $ingredient = new Ingredient();
                $ingredient->setName($compositionData->ingredientName);
                $this->ingredientRepository->save($ingredient);
            }

            $composition = new Composition();
            $composition->setIngredient($ingredient);
            $composition->setQuantity($compositionData->quantity);
            $composition->setUnit($compositionData->unit);
            $composition->setRecipe($recipe);
            $recipe->addComposition($composition);
        }
    }

    public function createCategories(array $categoryNames, Recipe $recipe): void
    {
        foreach ($categoryNames as $categoryName) {
            $category = $this->categoryRepository->findOneByName($categoryName);
            if (!$category) {
                $category = new Category();
                $category->setName($categoryName);
                $this->categoryRepository->save($category);
            }
            $recipe->addCategory($category);
        }
    }

    public function setRecipeDetails(Recipe $recipe, RecipeDetails $recipeDetails): void
    {
        $recipe->setDetails($recipeDetails->toArray());
    }
}
