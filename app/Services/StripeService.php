<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Token;

class StripeService
{
        public function getToken(Array $Data)
        {
            Stripe::setApiKey(config('app.Stripe.Private_Key'));
            try {
                return Token::create([
                    'card' => [
                        'number' => $Data['card_number'],
                        'exp_month' => $Data['expiry_month'],
                        'exp_year' => $Data['expiry_year'],
                        'cvc' => $Data['CVC'],
                    ],
                ]);

            }catch (\Exception $e){
                throw new \Exception('Error creating token: ' . $e->getMessage());
            }


        }


}
