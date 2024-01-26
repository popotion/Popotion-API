<?php

namespace App\Service;

use App\Entity\User;

interface PaymentHandlerInterface
{
    public function getPremiumCheckoutUrlFor(User $user): string;
    public function handlePaymentPremium($session): void;
    public function checkPaymentStatus($sessionId): bool;
}
