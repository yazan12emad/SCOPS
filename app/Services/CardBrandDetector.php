<?php

namespace App\Services;


class CardBrandDetector
{
    private String $cardBrand;

    public function getBrand():String
    {
        return $this->cardBrand;
    }
    public function detect(String $cardNumber): void
    {
        $cardNumber = preg_replace("/\D/", "", $cardNumber);
        $this->cardBrand = match(true){
            str_starts_with($cardNumber, '4')                       => 'Visa',
            (int)substr($cardNumber, 0, 2) >= 51
            && (int)substr($cardNumber, 0, 2) <= 55   => 'MasterCard',
            str_starts_with($cardNumber, '34') ||
            str_starts_with($cardNumber, '37')                     => 'American Express',
            default => 'Unknown',
        };
    }

}
