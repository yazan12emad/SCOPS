<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Http\Resources\CardResource;
use App\Models\Card;
use App\Services\CardService;
use App\Traits\ApiResponse;

class CardController extends Controller
{
    use ApiResponse;
    public function __construct(private CardService $cardService){}

    public function getCards()
    {
        $cards = auth()->user()
            ->cards()
            ->get();
        $message = $cards->isEmpty() ? "There is no cards" : "Cards retrieved successfully";

        return $this->success(CardResource::collection($cards), $message);
    }

    public function addCard(StoreCardRequest $request){
        try {
            $cardData = $request->validated();
            $user = auth()->user();
            $card = $this->cardService->addCard($cardData, $user);
            return $this->success(CardResource::make($card), "Card added successfully");
        }
        catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    public function changePrimary(UpdateCardRequest $request, card $card)
    {
        try {
            $this->cardService->changePrimaryCard($card);
            return $this->success(CardResource::make($card), "Card updated successfully");
        }
        catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    public function deleteCard(card $card)
    {
        try {
            $this->cardService->deleteCard($card);
        }
        catch (\Exception $exception){
            return $this->error($exception->getMessage() , $exception->getCode());
        }
        return $this->success([], "Card deleted successfully");
    }
}
