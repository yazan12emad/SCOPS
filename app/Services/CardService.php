<?php

namespace App\Services;

use App\Models\Card;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Random\RandomException;

class CardService
{
    public function __construct(private StripeService $stripeService ){}

    /**
     * @throws RandomException
     * @throws \Exception
     */
    public function addCard(array $data, User $user): Card
    {
        $paymentMethodId = $data['stripe_payment_method_id'] ?? null;

        if (! $paymentMethodId) {
            throw ValidationException::withMessages([
                'stripe_payment_method_id' => ['The stripe payment method id is required.'],
            ]);
        }

        // 1. Create Stripe customer if doesn't exist
        if (!$user->stripe_customer_id) {
            $customer = $this->stripeService->createCustomer($user);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        // 2. Fetch PaymentMethod from Stripe
        $pm = $this->stripeService->getPaymentMethod($paymentMethodId);

        // 3. Safety check — pm must not belong to another customer
        $pmCustomerId = is_string($pm->customer)
            ? $pm->customer
            : ($pm->customer?->id ?? null);

        if ($pmCustomerId && $pmCustomerId !== $user->stripe_customer_id) {
            throw new \Exception('This payment method belongs to another customer.');
        }

        // 4. Attach to customer if not already attached
        if (!$pmCustomerId) {
            $this->stripeService->attachPaymentMethod(
                $paymentMethodId,
                $user->stripe_customer_id
            );
            // Re-fetch after attach to get updated object
            $pm = $this->stripeService->getPaymentMethod($paymentMethodId);
        }

        // 5. First card → set as primary automatically
        $isFirstCard = Card::where('user_id', $user->user_id)->count() === 0;

        // 6. Save to DB — brand/last4/expiry come from Stripe, NOT Flutter
        $card = Card::create([
            'user_id'                  => $user->user_id,
            'card_holder_name'         => $data['card_holder_name'],
            'card_brand'               => $pm->card->brand     ?? 'unknown',
            'last4'                    => $pm->card->last4     ?? null,
            'expiry_month'             => $pm->card->exp_month ?? null,
            'expiry_year'              => $pm->card->exp_year  ?? null,
            'is_primary'               => $isFirstCard,
            'stripe_payment_method_id' => $pm->id,
        ]);

        if (!$card) {
            throw new \Exception('Failed to save card.');
        }
        return $card->refresh();
    }

//    public function addCard(array $data , User $user){
//        // 1. Create Stripe customer if user doesn't have one
//        if (!$user->stripe_customer_id) {
//                $customer = $this->stripeService->createCustomer($user);
//                $user->update(['stripe_customer_id' => $customer->id]);
//        }
//
//        $pm = $this->stripeService->getPaymentMethod($data['stripe_payment_method_id']);
//
//        $paymentMethodCustomerId = is_string($pm->customer)
//            ? $pm->customer
//            : ($pm->customer->id ?? null);
//
//        if ($paymentMethodCustomerId && $paymentMethodCustomerId !== $user->stripe_customer_id) {
//            throw new \Exception('This payment method belongs to another Stripe customer.');
//        }
//
//        // PaymentMethods confirmed by a SetupIntent are already attached to the customer.
//        // Attach only new, unused PaymentMethods.
//        if (!$paymentMethodCustomerId) {
//            $this->stripeService->attachPaymentMethod(
//                $data['stripe_payment_method_id'],  // pm_xxxxx from Flutter
//                $user->stripe_customer_id
//            );
//            $pm = $this->stripeService->getPaymentMethod($data['stripe_payment_method_id']);
//        }
//
//        $isFirstCard = Card::where('user_id', $user->user_id)->count() === 0;
////        $card = Card::create([
////            'user_id' => $user->user_id,
////            'card_holder_name' => $data['card_holder_name'],
////            'card_brand' => $data['card_brand'],
////            'last4' => $data['last4'],
////            'expiry_month' => $data['expiry_month'],
////            'expiry_year' => $data['expiry_year'],
////            'is_primary' => $isFirstCard,
////            'tokenized_pan' => $pm->id,
////        ]);
//
//        $card = Card::create([
//            'user_id' => $user->user_id,
//            'card_holder_name' => $data['card_holder_name'],
//            'card_brand' => $pm->card->brand ?? 'card',
//            'last4' => $pm->card->last4 ?? null,
//            'expiry_month' => $pm->card->exp_month ?? null,
//            'expiry_year' => $pm->card->exp_year ?? null,
//            'is_primary' => $isFirstCard,
//            'stripe_payment_method_id' => $pm->id,
//        ]);
//
//        if(!$card){
//            throw new \Exception('Error creating card');
//        }
//        return $card;
//    }

    /**
     * @throws \Exception
     */
    public function createSetupIntent(User $user): array
    {
        // Create Stripe customer if doesn't exist
        if (!$user->stripe_customer_id) {
            $customer = $this->stripeService->createCustomer($user);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        $setupIntent = $this->stripeService->createSetupIntent(
            $user->stripe_customer_id
        );

        return [
            'client_secret' => $setupIntent->client_secret,
            'customer_id'   => $user->stripe_customer_id,
        ];
    }

    public function deleteCard(card $card): void
    {
        if($card->user_id !== auth()->user()->user_id){
            throw new \Exception('Unauthorized to delete this card' , 401);
        }
        if($card->is_primary){
            throw new \Exception('Cannot delete primary card. Please set another card as primary before deleting this card.' , 409);
        }
        if($card->where('user_id', $card->user_id)->count() === 1){
            throw new \Exception('Cannot delete the only card. Please add another card before deleting this card.' , 409);
        }
        if(!$card->delete()){
            throw new \Exception('Error deleting card' ,500 );
        }
    }

    public function changePrimaryCard(card $card){
        if($card->user_id !== auth()->user()->user_id){
            throw new \Exception('Unauthorized to delete this card' , 401);
        }
        if($card->is_primary){
            throw new \Exception('The card already the primary card' , 500);
        }
        $card->where('user_id', $card->user_id)
            ->where('is_primary', 1)
            ->update(['is_primary' => 0]);

        return $card->update([
            'is_primary' => true
        ]);
    }



}
