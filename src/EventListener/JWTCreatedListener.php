<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $user = $event->getUser();
        if (!$user instanceof User) return;
        $payload['id'] = $user->getId();
        $payload['premium'] = $user->isPremium();
        $event->setData($payload);
    }
}
