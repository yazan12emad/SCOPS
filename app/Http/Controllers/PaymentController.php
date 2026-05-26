<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInitialPayment;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServicePlans;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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

            $data = $this->paymentService->createPaymentIntent($service, $plan);

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
    /**
     * GET /payments?subscription_id=X
     * Flutter polls this to check status + get receipt_url.
     */
    public function showPayment(Payment $payment): \Illuminate\Http\JsonResponse
    {
        abort_if($payment->user_id !== auth()->id(), 403);
        return $this->jsonResponse([
            'status' => $payment->status,
            'receipt_url' => $payment->receipt_url,
        ]);
    }

    public function confirmPayment(Payment $payment){
        if($payment->user_id !== auth()->id()){
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Unauthorized'
            ],403);
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

//
//    public function checkout(){
//        try {
//            Stripe::setApiKey(config('app.Stripe.Private_Key'));
//            $session = Session::create([
//                'payment_method_types' => ['card'],
//                'line_items' => [
//                    [
//                        'price_data' => [
//                            'currency' => 'usd',
//                            'product_data' => [
//                                'name' => 'SCOPS Subscription',
//                            ],
//                            'unit_amount' => 2000, // $20.00
//                        ],
//                        'quantity' => 1,
//                    ]
//                ],
//                'mode' => 'payment',
//                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
//                'cancel_url' => route('payment.cancel'),
//            ]);
//        }
//        catch (ApiErrorException $exception) {
//            report($exception);
//            return $this->jsonResponse([
//                'success' => false,
//                'message' => $exception->getMessage()
//            ]);
//
//        }
//
//        return $this->jsonResponse([
//            'checkout_url' => $session->url,
//            'session_id' => $session->id,
//        ]);
//    }
//
//    public function success(Request $request)
//    {
//        return response()->json([
//            'message' => 'Payment successful',
//            'session_id' => $request->session_id,
//        ]);
//    }
//
//    public function cancel()
//    {
//        return response()->json([
//            'message' => 'Payment canceled',
//        ]);
//    }


//    public function makeReceipt(Payment $payment)
//    {
//        if ($payment->user()->user_id !== auth()->id()) {
//            abort(403);
//        }
//        try {
//            $this->paymentService->saveSuccessPayment($payment);
//            return $this->jsonResponse([
//                'success' => true,
//                'message' => 'Payment successful',
//                'payment' => $payment
//            ]);
//        }
//        catch (Throwable $e) {
//            return $this->jsonResponse([
//                'success' => false,
//                'message' => $e->getMessage()
//            ]);
//        }
//    }




}
