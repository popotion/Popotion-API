<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PaymentHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Webhook;

class PremiumController extends AbstractController
{
    public function __construct(
        private PaymentHandlerInterface $paymentHandlerInterface,
        private UserRepository $userRepository,
        #[Autowire('%stripe_webhook_key%')] private string $stripeWebhookKey,
    ) {
    }

    #[Route('/api/premium/subscribe', name: 'premium_subscribe', methods: ['POST'])]
    public function subscribe(): Response
    {
        $user = $this->getUser();
        if ($user instanceof User && $user->isPremium())
            return $this->json(['message' => 'Vous êtes déjà premium'], Response::HTTP_BAD_REQUEST);
        $checkoutUrl = $this->paymentHandlerInterface->getPremiumCheckoutUrlFor($user);
        return $this->json(['checkoutUrl' => $checkoutUrl], Response::HTTP_OK);
    }

    #[Route('/api/premium/webhook', name: 'premium_webhook', methods: ['POST'])]
    public function webhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $this->stripeWebhookKey
            );
            if ($event->type === "checkout.session.completed") {
                $session = $event->data->object;
                $this->paymentHandlerInterface->handlePaymentPremium($session);
                return $this->json(['message' => 'success'], Response::HTTP_OK);
            } else {
                return $this->json(['message' => 'error'], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            return $this->json(['message' => 'error'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/premium/confirm', name: 'premium_confirm', methods: ['POST'])]
    public function confirm(Request $request): Response
    {
        $sessionId = $request->getContent();
        if (!$this->paymentHandlerInterface->checkPaymentStatus($sessionId))
            return $this->json(['message' => 'success'], Response::HTTP_OK);
        return $this->json(['message' => 'error'], Response::HTTP_BAD_REQUEST);
    }
}
