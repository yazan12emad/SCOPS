<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'card_id'=>$this->card_id ,
            'card_holder_name'=>$this->card_holder_name,
            'card_brand'=>$this->card_brand,
            'card_number' => '**** **** **** ' . $this->last4 ,
            'expired_date' => str_pad($this->expiry_month, 2, '0', STR_PAD_LEFT) . '/' . $this->expiry_year,
            'is_primary' => (bool)$this->is_primary? 'yes':'no',
        ];
    }
}
