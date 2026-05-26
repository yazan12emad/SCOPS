<?php

//In my Laravel Blade receipt template, images are broken when viewed
//on external devices (Flutter app, browser outside the server) because
//all image tags use public_path() which generates local server file
//paths that are inaccessible externally.
//
//Fix the following:
//
//1. Replace every occurrence of public_path() in the Blade template
//   with asset() so images generate full public URLs accessible from
//   anywhere.
//
//2. Make sure APP_URL in .env is respected so asset() generates the
//   correct base URL.
//
//3. If this receipt is rendered as a PDF (using DomPDF or Snappy),
//   asset() will also fail because PDF renderers can't fetch HTTP URLs.
//   In that case, embed all images as Base64 strings instead:
//   - In the ReceiptService or wherever the view is rendered, read each
//     image with file_get_contents(public_path('asset/image.png')),
//     encode it with base64_encode(), and pass it to the view.
//   - In the Blade template, use the base64 data URI as the src instead
//     of any path or URL.
//
//The images are located in /public/asset/ and include:
//- owl_cyan.png
//- scops_white.png
//- payment_receipt.png
//
//Apply the correct fix based on whether the receipt is an HTML page
//or a PDF file.

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Service;
use App\Models\ServicePlans;
use App\Services\ReceiptService;
use App\Services\StripeService;
use Stripe\Exception\ApiErrorException;

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
    public function createPaymentIntent(Service $service, ServicePlans $plan): array
    {
        $user = auth()->user();
        if (!$user->stripe_customer_id) {
            $customer = $this->stripeService->createCustomer($user);
            $user->update(['stripe_customer_id' => $customer->id]);
        }
        $amount = $plan->price ?? $service->default_amount; // ← price not amount
        $payment = Payment::create([
            'user_id'    => $user->user_id,
            'service_id' => $service->id,
            'plan_id'    => $plan->id,
            'amount'     => $amount,
            'status'     => 'pending',
            'currency'   => 'usd',
        ]);
        if(!$payment){
            throw new \Exception('Add payment failed');
        }

        $data = [
            'amount'   => (int) ($amount * 100),
            'currency' => 'usd',
            'customer' => $user->stripe_customer_id,
            'setup_future_usage' => 'off_session',
            'metadata' => [
                'payment_id' => $payment->payment_id,
                'user_id'    => $user->user_id,
                'service_id' => $service->id,
                'plan_id'    => $plan->id,
            ],
            'automatic_payment_methods' => [
                'enabled'         => true,
                'allow_redirects' => 'never',
            ],
        ];

        $intent = $this->stripeService->createPaymentIntent($data);

        $updated = $payment->update([
            'gateway_reference' => $intent->id,
        ]);

        if (!$updated) {
            throw new \Exception('Failed to update payment record with intent ID');
        }

        return [
            'client_secret' => $intent->client_secret,
            'payment_id'    => $payment->payment_id,
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
