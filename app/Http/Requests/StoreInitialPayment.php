<?php

namespace App\Http\Requests;

use App\Rules\EmailCheckInPayment;
use App\Services\CardBrandDetector;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreInitialPayment extends FormRequest
{
    protected $stopOnFirstFailure = true;
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $service = $this->route('service'); // Service model (auto-resolved)
        return [
                'email' => ['required', 'email' , new EmailCheckInPayment(
                    planId:$this->input('plan_id'),
                    serviceId: $service?->id
                )] ,
                 'plan_id' => ['required', 'integer', Rule::exists('service_plans', 'id')],
        ];
    }

    public function messages(): array{
        return [
            'plan_id.required' => 'The plan ID field is required.',
            'plan_id.integer' => 'The plan ID must be an integer.',
            'plan_id.exists' => 'The plan ID does not exist.',
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
