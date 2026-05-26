<?php

namespace App\Http\Requests;

use App\Services\CardBrandDetector;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreCardRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;
    public function authorize(): bool
    {
        return auth()->check();
    }
//    protected function prepareForValidation(): void
//    {
//        if($this->card_number) {
//            $cardDetector = new CardBrandDetector();
//            $cardDetector->detect($this->card_number);
//            $this->merge([
//                'card_brand' => $cardDetector->getBrand(),
//                'last4' => substr($this->card_number, -4),
//            ]);
//        }
//
//        if($this->expiry_month && $this->expiry_year){
//            $expiryDate = \DateTime::createFromFormat('m/Y', $this->expiry_month . '/' . $this->expiry_year);
//            if($expiryDate){
//                $this->merge([
//                    'expiry_month' => (int)$expiryDate->format('m'),
//                    'expiry_year' => (int)$expiryDate->format('Y'),
//                    'expiry_date' => $expiryDate->format('Y-m-d')
//                ]);
//            }
//        }
//}
    public function rules(): array
    {
//        $firstOfCurrentMonth = date('Y-m-01');
        return [
                 'stripe_payment_method_id' => ['required', 'string'],
                 'card_holder_name'=> ['required', 'string', 'max:100'],
//            'card_holder_name' => ['required', 'string', 'max:100'],
//            'card_brand'=> ['required', 'string', Rule::in(['Visa', 'MasterCard', 'American Express'])],
//            'last4'=> ['required', 'string', 'digits:4'],
//            'expiry_month'=> ['required', 'integer', 'between:1,12'],
//            'expiry_year'=> ['required', 'integer', 'min:' . date('Y'), 'max:' . date('Y', strtotime('+20 years'))],
//            'expiry_date' => ['date', 'date_format:Y-m-d', 'after_or_equal:' . $firstOfCurrentMonth],
//            'card_number'=> ['required', 'numeric', 'digits:16'],
////            'CVC' => ['required', 'numeric', 'digits:3'],
        ];
    }

    public function messages(): array{
        return [
//            'card_brand.in' => 'Card brand must be one of the following: Visa, MasterCard, American Express',
//            'last4.digits' => 'Last 4 digits must be exactly 4 digits',
//            'expiry_month.between' => 'Expiry month must be between 1 and 12',
//            'expiry_year.min' => 'Expiry year must be the current year or later',
//            'card_number.digits' => 'Card number must be exactly 16 digits',
//            'expiry_date.after_or_equal' => 'This card is expired' ,
//            'CVC.numeric' => 'CVC must be a numeric value',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message'  => $validator->errors(),
            ], 422)
        );
    }
}
