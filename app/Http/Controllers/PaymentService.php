<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\BalanceTransaction;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServicePlans;
use App\Models\Subscription;
use App\Services\ReceiptService;
use App\Services\StripeService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;

class PaymentService
{
    public function __construct(
        private ReceiptService $receiptService,
        private StripeService  $stripeService)
    {
    }

    /**
     * Create a pending Payment + a Stripe PaymentIntent.
     * Returns the client_secret Flutter needs to confirm natively.
     * @throws ApiErrorException
     * @throws \Exception
     */
    public function createPaymentIntent(Service $service, ServicePlans $plan, string $serviceEmail): array
    {
        $user = auth()->user();
        $card = $user->cards()->where('is_primary', true)->first();
        if (!$card) {
            throw new \Exception('There is no primary card for this user. Please add a card before making a payment.');
        }

        if (!$user->stripe_customer_id) {
            $customer = $this->stripeService->createCustomer($user);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        $amount = $plan->price ?? $service->default_amount; // ← price not amount
        if (!$this->checkBalance($card, $amount)) {
             Payment::create([
                'user_id' => $user->user_id,
                'service_id' => $service->id,
                'plan_id' => $plan->id,
                'service_email' => $serviceEmail,
                'amount' => $amount,
                'status' => 'failed',
                'currency' => 'usd',
            ]);
            throw new \Exception('The primary card does not have enough balance.');
        }
        $payment = Payment::create([
            'user_id' => $user->user_id,
            'service_id' => $service->id,
            'plan_id' => $plan->id,
            'service_email' => $serviceEmail,
            'amount' => $amount,
            'status' => 'pending',
            'currency' => 'usd',
        ]);
        if (!$payment) {
            throw new \Exception('Add payment failed');
        }
        $data = [
            'amount' => (int)($amount * 100),
            'currency' => 'usd',
            'customer' => $user->stripe_customer_id,
            'setup_future_usage' => 'off_session',
            'metadata' => [
                'payment_id' => $payment->payment_id,
                'user_id' => $user->user_id,
                'service_id' => $service->id,
                'plan_id' => $plan->id,
            ],
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
            ],
        ];
        try {
            $intent = $this->stripeService->createPaymentIntent($data);
        } catch (\Exception $exception) {
            throw new \Exception('Stripe error while create payment intent' . $exception->getMessage());
        }

        $updated = $payment->update([
            'gateway_reference' => $intent->id,
        ]);

        if (!$updated) {
            throw new \Exception('Failed to update payment record with intent ID');
        }

        return [
            'client_secret' => $intent->client_secret,
            'payment_id' => $payment->payment_id,
        ];
    }
    /**
     * @throws \Exception
     */
    public function confirmPayment(Payment $payment)
    {
        $card = $payment->user->cards()->where('is_primary', true)->first();
        if (!$card) {
            throw new \Exception('No primary card found for this user');
        }
        if (!$card->stripe_payment_method_id) {
            throw new \Exception('The primary card does not have a Stripe payment method.');
        }
        $intent = $this->stripeService->confirmPaymentIntent(
            $payment->gateway_reference,        // pi_xxxxx
            $card->stripe_payment_method_id     // pm_xxxxx
        );
        if ($intent->status === 'succeeded') {
            $this->handleSuccess($payment, $intent->id);
            $payment->refresh();
            return [
                'success' => true,
                'payment_id' => $payment->payment_id,
                'status' => $intent->status,
                'receipt_url' => $payment->receipt_url,
                'message' => 'Payment confirmed — receipt will be ready shortly',
            ];
        }
        if ($intent->status === 'requires_action') {
            return [
                'success' => false,
                'status' => 'requires_action',
                'message' => '3D Secure required',
                'next_action_url' => $intent->next_action->redirect_to_url->url ?? null,
            ];
        }
        throw new \Exception('Payment failed with status: ' . $intent->status);
    }
    public function handleSuccess(Payment $payment, string $intentId): void
    {
        try {
            $payment = DB::transaction(function () use ($payment, $intentId) {
                $payment = Payment::query()
                    ->whereKey($payment->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($payment->status === 'successful' && $payment->subscription_id) {
                    return $payment;
                }
                $subscription = $payment->subscription ?: $this->addPaymentToSubscriptionTable($payment);

                $card = Card::query()
                    ->whereKey($subscription->card_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$this->checkBalance($card, $payment->amount)) {
                    throw new \Exception('The primary card with numbers ' . $card->last4 . ' does not have enough balance.');
                }

                $newBalance = $card->balance - $payment->amount;

                $card->update([
                    'balance' => $newBalance,
                ]);

                $payment->update([
                    'status' => 'successful',
                    'gateway_reference' => $intentId,
                    'subscription_id' => $subscription->id,
                ]);

                BalanceTransaction::create([
                    'card_id' => $card->card_id,
                    'user_id' => $payment->user_id,
                    'subscription_id' => $subscription->id,
                    'amount' => $payment->amount,
                    'balance' => $newBalance,
                    'type' => 'payment',
                ]);

                return $payment->fresh(['subscription.service', 'user']);
            });
            if (!$payment->receipt_url) {
                $receiptUrl = $this->receiptService->generateReceipt($payment);
                $payment->update(['receipt_url' => $receiptUrl]);
            }
        } catch (\Exception $e) {
            \Log::error('Error handling successful payment: ' . $e->getMessage(), [
                'payment_id' => $payment->payment_id,
                'intent_id' => $intentId,
            ]);
            throw new \Exception('An error occurred while processing the payment. Please contact support.');
        }
    }

    public function addPaymentToSubscriptionTable(Payment $payment): Subscription
    {
        $payment->loadMissing(['user', 'service', 'plan']);
        $card = $payment->user
            ->cards()
            ->where('is_primary', true)
            ->first();
        if (!$card) {
            throw new \Exception('No primary card found for this user');
        }
        if (!$payment->plan) {
            throw new \Exception('Payment plan not found.');
        }
        $startDate = now();
        return Subscription::create([
            'user_id' => $payment->user_id,
            'service_id' => $payment->service_id,
            'card_id' => $card->card_id,
            'plan_id' => $payment->plan_id,
            'email' => $payment->service_email,
            'amount' => $payment->amount,
            'billing_cycle' => $payment->plan->billing_cycle,
            'start_date' => $startDate->toDateString(),
            'renewal_date' => $this->renewalDate($startDate, $payment->plan->billing_cycle)->toDateString(),
            'status' => 'active',
        ]);
    }

    public function renewalDate(Carbon $startDate, string $billingCycle): Carbon
    {
        return match ($billingCycle) {
            'weekly' => $startDate->copy()->addWeek(),
            'monthly' => $startDate->copy()->addMonth(),
            'yearly' => $startDate->copy()->addYear(),
            default => throw new \Exception("Unsupported billing cycle: {$billingCycle}"),
        };
    }

    public function checkBalance(Card $card, $amount): bool
    {
        {
            $currentBalance = $card->balance;
            return $currentBalance >= $amount;
        }
    }

    public function changeBalance(Card $card, Payment $payment)
    {
        if (!$card->update([
            'balance' => $card->balance - $payment->amount,
        ])) {
            return false;
        }
        return true;
    }
}
