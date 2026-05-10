<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RegisterUserValidation extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected $stopOnFirstFailure = true;

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:20', Rule::unique('users', 'username')],
            'email'=> ['required', 'string', 'email:rfc,dns', 'max:30', Rule::unique('users', 'email')],
            'password'=> ['required', 'string', 'min:8'],
            'phone'=> ['required', 'digits:10', Rule::unique('users', 'phone')],
        ];
    }
    public function messages() : array
    {
        return [
            'username.required' => 'Username is required',
            'username.string' => 'Username must be string',
            'username.unique' => 'Username already exists',
            'email.required' => 'Email is required',
            'email.string' => 'Email must be string',
            'email.unique' => 'Email already exists',
            'password.min' => 'Password must be at least 8 characters',
            'password.required' => 'Password is required',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must be 10 digits',

        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422)
        );
    }

}
