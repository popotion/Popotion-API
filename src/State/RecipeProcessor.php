<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Recipe;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RecipeProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] private ProcessorInterface $persistProcessor,
        private Security $security
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->security->getUser();
        $this->setAuthor($data, $user);
        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    public function setAuthor(Recipe $recipe, mixed $author): void
    {
        if ($author != null)
            $recipe->setAuthor($author);
    }
}
