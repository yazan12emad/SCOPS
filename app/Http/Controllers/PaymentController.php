<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInitialPayment;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServicePlans;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}
    /**
     * POST /payments/{service}
     * Flutter calls this → gets back client_secret code to confirm natively.
     * add the payment as pending
     */

    public function MakePayment(Service $service, StoreInitialPayment $request)
    {
        try {
            $plan = ServicePlans::where('id', $request->plan_id)
                ->where('service_id', $service->id)
                ->firstOrFail();
            $data = $this->paymentService->createPaymentIntent($service, $plan , $request->email);
            return $this->jsonResponse([
                'success'       => true,
                'client_secret' => $data['client_secret'],
                'payment_id'    => $data['payment_id'],
                'message'       => 'Payment intent created successfully'
            ]);
        } catch (\Exception $exception) {
            return $this->jsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    /**
     * GET /payments?subscription_id=X
     * Flutter polls this to check status + get receipt_url.
     */
    public function showPayment(Payment $payment): \Illuminate\Http\JsonResponse
    {
        if($payment->user_id !== auth()->id()){
        return $this->jsonResponse([
            'success' => false,
            'message' => "You don't have permission to access this page"
        ] ,403);
    }
        return $this->jsonResponse([
            'status' => $payment->status,
            'receipt_url' => $payment->receipt_url,
        ]);
    }

    public function confirmPayment(Payment $payment){
        if($payment->user_id !== auth()->id()){
            return $this->jsonResponse([
                'success' => false,
                'message' => "You don't have permission to access this page"
            ] ,403);
        }

        try{
            $result = $this->paymentService->confirmPayment($payment);
            return $this->jsonResponse(
                $result, 200
            );
        }
        catch(\Exception $exception){
            return  $this->jsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ],500);
        }
    }

    /**
     * POST /webhook  (no auth middleware — Stripe signs the request)
     * Always return 200 fast; do heavy work in a queued job in production.
     */

    public function webhook(Request $request){
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('app.Stripe.STRIPE_WEBHOOK_SECRET');
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }
        if($event->type === 'payment_intent.succeeded') {
            $intent    = $event->data->object;
            $paymentId = $intent->metadata->payment_id ?? null;
            $payment   = Payment::find($paymentId);
            if ($payment && $payment->status !== 'successful') {
                $this->paymentService->handleSuccess($payment, $intent->id);
            }
        }
        return response('ok', 200);
    }





}
