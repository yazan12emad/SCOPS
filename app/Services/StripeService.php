<?php

namespace App\Services;

use App\Models\User;
use Stripe\StripeClient;
use Stripe\PaymentIntent;

class StripeService
{
    private StripeClient $connection;

    public function __construct()
    {
        $this->connection = new StripeClient(config('app.Stripe.secret'));
    }

    public function createCustomer(User $user): \Stripe\Customer{
        $result =  $this->connection->customers->create([
            'email' => $user->email,
            'name' => $user->first_name.' '.$user->last_name,
            'metadata' => ['user_id' => $user->user_id],
        ]);

        if(!$result){
            throw new \Exception('Error creating Stripe customer');
        }
        return $result;
    }

    public function attachPaymentMethod(string $paymentMethodId, string $customerId): \Stripe\PaymentMethod
    {
        $result = $this->connection->paymentMethods->attach($paymentMethodId, [
            'customer' => $customerId,
        ]);
        if (!$result) {
            throw new \Exception('Error while attaching payment method');
        }
        return $result;
    }

    public function createSetupIntent(string $customerId): \Stripe\SetupIntent
    {
         $result = $this->connection->setupIntents->create([
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'usage' => 'off_session',
        ]);
         if(!$result){
             throw new \Exception('Error creating setup intent');
         }
         return $result;
    }

    public function getPaymentMethod(string $paymentMethodId): \Stripe\PaymentMethod
    {
        $result =  $this->connection->paymentMethods->retrieve($paymentMethodId);
        if (!$result) {
            throw new \Exception('Error while retrieving payment method');
        }
        return $result;
    }

    public function createPaymentIntent(array $data): PaymentIntent
    {
        return $this->connection->paymentIntents->create($data);
    }

    public function confirmPaymentIntent(string $intentId, string $paymentMethodId): \Stripe\PaymentIntent
    {
        return $this->connection->paymentIntents->confirm($intentId, [
            'payment_method' => $paymentMethodId,
        ]);
    }
}
