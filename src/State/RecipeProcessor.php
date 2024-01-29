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

        if (!empty($data->getCompositionsData())) $this->createIngredientsAndSetCompositions($data, $data->getCompositionsData());

        if (!empty($data->getCategoryNames())) $this->createCategories($data->getCategoryNames(), $data);

        if ($data->getRecipeDetails() !== null) $this->setRecipeDetails($data, $data->getRecipeDetails());

        $this->setContainsAlcohol($data, $data->getCategoryNames(), $data->getCompositionsData());

        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    public function setAuthor(Recipe $recipe, mixed $author): void
    {
        if ($author == null) return;
        $recipe->setAuthor($author);
    }

    public function createIngredientsAndSetCompositions(Recipe $recipe, array $compositionsData): void
    {
        $updatedCompositions = [];

        foreach ($compositionsData as $compositionData) {
            $ingredient = $this->ingredientRepository->findOneByName($compositionData->ingredientName);

            if (!$ingredient) {
                $ingredient = new Ingredient();
                $ingredient->setName($compositionData->ingredientName);
                $this->ingredientRepository->save($ingredient);
            }

            $found = false;
            foreach ($recipe->getCompositions() as $composition) {
                if ($composition->getIngredient() === $ingredient) {
                    $composition->setQuantity($compositionData->quantity);
                    $composition->setUnit($compositionData->unit);
                    $found = true;
                    $updatedCompositions[] = $composition;
                    break;
                }
            }

            if (!$found) {
                $composition = new Composition();
                $composition->setIngredient($ingredient);
                $composition->setQuantity($compositionData->quantity);
                $composition->setUnit($compositionData->unit);
                $composition->setRecipe($recipe);
                $recipe->addComposition($composition);
                $updatedCompositions[] = $composition;
            }
        }

        foreach ($recipe->getCompositions() as $composition)
            if (!in_array($composition, $updatedCompositions))
                $recipe->removeComposition($composition);
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

    public function containsAlcohol(Recipe $recipe, array $categoryNames, array $compositionsData): bool
    {
        $res = false;
        foreach ($categoryNames as $categoryName)
            if (strtolower($categoryName) === 'alcool') $res = true;

        $ingredientsWithAlcohol = ['absinthe', 'amaretto', 'aquavit', 'bailey\'s', 'bière', 'calvados', 'campari', 'champagne', 'chartreuse', 'cidre', 'cognac', 'cointreau', 'curacao', 'gin', 'jagermeister', 'kirsh', 'limoncello', 'liqueur', 'malibu', 'manzana', 'marc', 'martini', 'metaxa', 'ouzo', 'pastis', 'picon', 'pina colada', 'porto', 'raki', 'rhum', 'sake', 'sambuca', 'sangria', 'schnaps', 'sherry', 'southern comfort', 'tequila', 'triple sec', 'vermouth', 'vin', 'vodka', 'whisky', 'xeres', 'zubrowka'];

        foreach ($compositionsData as $compositionData)
            if (in_array(strtolower($compositionData->ingredientName), $ingredientsWithAlcohol)) $res = true;

        return $res;
    }

    public function setContainsAlcohol(Recipe $recipe, array $categoryNames, array $compositionsData): void
    {
        $recipe->setContainsAlcohol($this->containsAlcohol($recipe, $categoryNames, $compositionsData));
    }
}
