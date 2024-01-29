<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';

    public const DELETE = 'USER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof UserInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) return false;

        switch ($attribute) {
            case self::EDIT:
                if ($subject == $user || in_array('ROLE_ADMIN', $user->getRoles()))
                    return true;
                break;
            case self::DELETE:
                if (($subject == $user) || (in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_ADMIN', $subject->getRoles())))
                    return true;
                break;
        }

        return false;
    }
}
