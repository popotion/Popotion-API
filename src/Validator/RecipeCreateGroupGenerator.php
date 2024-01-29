<?php

namespace App\Validator;

use ApiPlatform\Symfony\Validator\ValidationGroupsGeneratorInterface;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\GroupSequence;
use App\Entity\Recipe;
use Symfony\Bundle\SecurityBundle\Security;

class RecipeCreateGroupGenerator implements ValidationGroupsGeneratorInterface
{
    public function __construct(
        private Security $security
    ) {
    }

    public function __invoke(object $object): array|GroupSequence
    {
        assert($object instanceof Recipe);
        $user = $this->security->getUser();
        $group = 'recipe:create:normal';
        $user instanceof User && $user->isPremium() ? $group = 'recipe:create:premium' : 'recipe:create:normal';
        return ['Default', $group];
    }
}
