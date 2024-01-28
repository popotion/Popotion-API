<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Stripe\StripeClient;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentHandler implements PaymentHandlerInterface
{

    public function __construct(
        private readonly UserRepository $userRepository,
        #[Autowire('%premium_price%')] private string $premiumPrice,
        #[Autowire('%stripe_api_key%')] private string $stripeApiKey,
        #[Autowire('%stripe_success_url%')] private string $stripeSuccessUrl,
        #[Autowire('%stripe_cancel_url%')] private string $stripeCancelUrl,
    ) {
    }

    public function getPremiumCheckoutUrlFor(User $user): string
    {
        $paymentData = [
            'mode' => 'payment',
            'payment_intent_data' => [
                'capture_method' => 'manual',
                'receipt_email' => $user->getMailAdress()
            ],
            'customer_email' => $user->getMailAdress(),
            'success_url' => $this->stripeSuccessUrl,
            'cancel_url' => $this->stripeCancelUrl,
            "metadata" => [
                "userId" => $user->getId(),
            ],
            "line_items" => [
                [
                    "price_data" => [
                        "currency" => "eur",
                        "product_data" => [
                            "name" => "Popotion premium"
                        ],
                        "unit_amount" => $this->premiumPrice * 100
                    ],
                    "quantity" => 1
                ],
            ]
        ];
        Stripe::setApiKey($this->stripeApiKey);
        $stripeSession = Session::create($paymentData);
        return $stripeSession->url;
    }

    public function handlePaymentPremium($session): void
    {
        $userId = $session['metadata']['userId'];
        $user = $this->userRepository->findOneById($userId);
        $paymentIntent = $session['payment_intent'];
        $stripe = new StripeClient($this->stripeApiKey);
        if ($user == null) {
            $stripe->paymentIntents->cancel($paymentIntent);
            throw new \Exception("L'utilisateur n'a pas été trouvé...");
        }
        if ($user->isPremium()) {
            $stripe->paymentIntents->cancel($paymentIntent);
            throw new \Exception("L'utilisateur est déjà premium...");
        }
        $paymentCapture = $stripe->paymentIntents->capture($paymentIntent, []);
        if ($paymentCapture == null || $paymentCapture["status"] != "succeeded") {
            throw new \Exception("Le paiement n'a pas pu être complété...");
        }
        $user->setPremium(true);
        $this->userRepository->save($user);
    }

    public function checkPaymentStatus($sessionId): bool
    {
        $stripe = new StripeClient($this->stripeApiKey);
        $session = $stripe->checkout->sessions->retrieve($sessionId);
        $paymentIntentId = $session->payment_intent;
        $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);
        $status = $paymentIntent->status;
        return $status == "succeeded";
    }
}
