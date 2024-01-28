<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\User;
use App\Entity\Recipe;

class RecipeVoter extends Voter
{
    public const EDIT = 'RECIPE_EDIT';
    public const CREATE = 'RECIPE_CREATE';
    public const DELETE = 'RECIPE_DELETE';
    public const READ = 'RECIPE_READ';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::CREATE, self::DELETE, self::READ])
            && $subject instanceof Recipe || null === $subject;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($attribute == self::READ && !$user)
            if (!$subject->containsAlcohol())
                return true;

        if (!$user instanceof User) return false;

        switch ($attribute) {
            case self::EDIT:
                if ($subject->getAuthor() == $user || in_array('ROLE_ADMIN', $user->getRoles()))
                    return true;
                break;
            case self::CREATE:
                if (in_array('ROLE_USER', $user->getRoles()))
                    return true;
                break;
            case self::DELETE:
                if ($subject->getAuthor() == $user || in_array('ROLE_ADMIN', $user->getRoles()))
                    return true;
                break;
            case self::READ:
                if ($subject->containsAlcohol() && !$user->isUnder18())
                    return true;
                break;
        }

        return false;
    }
}
