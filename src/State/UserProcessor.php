<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $this->verifyCurrentPlainPassword($data, $data->getCurrentPlainPassword());
        $this->encryptPassword($data, $data->getPlainPassword());
        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    public function encryptPassword(User $user, ?string $plainPassword): void
    {
        if ($plainPassword == null) return;
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
    }

    public function verifyCurrentPlainPassword(User $user, ?string $currentPlainPassword): bool
    {
        if ($currentPlainPassword == null) return false;
        return $this->passwordHasher->isPasswordValid($user, $currentPlainPassword) ? true : throw new AccessDeniedHttpException('Le mot de passe actuel est incorrect');
    }
}
