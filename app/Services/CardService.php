<?php

namespace App\Services;

use App\Models\Card;
use App\Models\User;

class CardService
{
    public function addCard(array $data , User $user){

//        $Stripe = new StripeService();
//        $token = $Stripe->getToken($data);

//        if($token->status !== 'succeeded'){
//            throw new \Exception('Error tokenizing card');
//        }

        $fakeToken = 'tok_' . bin2hex(random_bytes(16));

        $isFirstCard = Card::where('user_id',$user->user_id )->count() === 0;

        $card = Card::create([
            'user_id' => $user->user_id,
            'card_holder_name' => $data['card_holder_name'],
            'card_brand' => $data['card_brand'],
            'last4' => $data['last4'],
            'expiry_month' => $data['expiry_month'],
            'expiry_year' => $data['expiry_year'],
            'is_primary' => $isFirstCard,
            'tokenized_pan' => $fakeToken,
        ]);
        if(!$card){
            throw new \Exception('Error creating card');
        }
        return $card;

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
