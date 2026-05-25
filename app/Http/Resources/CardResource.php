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
            'card_id'          => $this->card_id,
            'card_holder_name' => $this->card_holder_name,
            'card_brand'       => $this->card_brand,
            'last4'            => $this->last4,
            'expiry_month'     => $this->expiry_month,
            'expiry_year'      => $this->expiry_year,
            'is_primary'       => (bool) $this->is_primary,
        ];
    }
}
