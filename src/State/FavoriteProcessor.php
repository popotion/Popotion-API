<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Favorite;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class FavoriteProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] private ProcessorInterface $persistProcessor,
        private Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->security->getUser();
        $this->setUser($data, $user);
        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    public function setUser(Favorite $favorite, mixed $user): void
    {
        if ($user == null) return;
        $favorite->setUser($user);
    }
}
