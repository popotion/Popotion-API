<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const CREATE = 'COMMENT_CREATE';

    public const DELETE = 'USER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::DELETE])
            && $subject instanceof Comment || null === $subject;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) return false;

        switch ($attribute) {
            case self::CREATE:
                if (in_array('ROLE_USER', $user->getRoles()))
                    return true;
                break;
            case self::DELETE:
                if ($subject == $user || in_array('ROLE_ADMIN', $user->getRoles()))
                    return true;
                break;
        }

        return false;
    }
}
