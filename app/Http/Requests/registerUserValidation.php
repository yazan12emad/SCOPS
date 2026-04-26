<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class registerUserValidation extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|max:20|unique:users,username',
            'email' => 'required|string|email|max:30|unique:users',
            'password' => 'min:8|required',
            'phone'=> 'required|regex:/^\d{10}$/',
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
