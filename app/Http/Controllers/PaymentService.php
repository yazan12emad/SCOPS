<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Service;
use App\Services\ReceiptService;
use App\Services\StripeService;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;

class PaymentService
{
    public function __construct(
        private ReceiptService $receiptService,
        private StripeService  $stripeService) {}

    /**
     * Create a pending Payment + a Stripe PaymentIntent.
     * Returns the client_secret Flutter needs to confirm natively.
     * @throws ApiErrorException
     * @throws \Exception
     */
    public function createPaymentIntent(Service $service): array
    {
        $user = auth()->user();
        if (!$user->stripe_customer_id) {
            $customer = $this->stripeService->createCustomer($user);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        // 1. Create pending payment record
        $payment = Payment::create([
            'user_id'    => $user->user_id,
            'service_id' => $service->id,
            'amount'     => $service->default_amount,
            'status'     => 'pending',
            'currency'   => 'usd',
        ]);

        // 2. Build Stripe payload
        $data = [
            'amount'   => (int) ($service->default_amount * 100), // must be int
            'currency' => 'usd',
            'customer' => $user->stripe_customer_id,
            'setup_future_usage' => 'off_session',
            'metadata' => [
                'payment_id' => $payment->payment_id,  // ← $payment->id not payment_id
                'user_id'    => $user->user_id,
                'service_id' => $service->id,
            ],
            'automatic_payment_methods' => [
                'enabled'         => true,
                'allow_redirects' => 'never',
            ],
        ];

        // 3. Create Stripe PaymentIntent
        $intent = $this->stripeService->createPaymentIntent($data);

        // 4. Save intent ID to payment record
        $updated = $payment->update([
            'gateway_reference' => $intent->id,  // ← object not array
        ]);

        if (!$updated) {
            throw new \Exception('Failed to update payment record with intent ID');
        }

        // 5. Return to controller
        return [
            'client_secret' => $intent->client_secret,  // ← consistent: always object
            'payment_id'    => $payment->payment_id,             // ← $payment->id
        ];
    }
    public function handleSuccess(Payment $payment, string $intentId): void
    {
        {
            // Update payment record as success
            $payment->update([
                'status' => 'successful',
                'gateway_reference' => $intentId,
            ]);
            // generate the receipt and save the URL in the payment record
            $receiptUrl = $this->receiptService->generateReceipt($payment);
            $payment->update(['receipt_url' => $receiptUrl]);
        }
    }

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
                'status'  => $intent->status,
                'receipt_url' => $payment->receipt_url,
                'message' => 'Payment confirmed — receipt will be ready shortly',
            ];


        }
        if ($intent->status === 'requires_action') {
            return [
                'success'        => false,
                'status'         => 'requires_action',
                'message'        => '3D Secure required',
                'next_action_url'=> $intent->next_action->redirect_to_url->url ?? null,
            ];
        }
        throw new \Exception('Payment failed with status: ' . $intent->status);



    }
}
