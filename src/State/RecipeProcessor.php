<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Composition;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RecipeProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] private ProcessorInterface $persistProcessor,
        private Security $security,
        private IngredientRepository $ingredientRepository
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->security->getUser();
        $this->setAuthor($data, $user);

        foreach ($data->getCompositionsData() as $compositionData) {
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
            $composition->setRecipe($data);
            $data->addComposition($composition);
        }

        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    public function setAuthor(Recipe $recipe, mixed $author): void
    {
        if ($author == null) return;
        $recipe->setAuthor($author);
    }
}
