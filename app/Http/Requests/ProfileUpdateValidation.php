<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ProfileUpdateValidation extends FormRequest
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
    //Rule::unique('users', 'email')->ignore($this->route('user')->id),
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'username' => 'sometimes|string|max:20' ,
            Rule::unique('users' , 'username')->ignore($userId ,'user_id'),
            'email' => 'sometimes|string|email|max:30' ,
            Rule::unique('users', 'email')->ignore($userId,'user_id'),
            'phone'=> 'sometimes|regex:/^\d{10}$/' ,
            Rule::unique('users', 'phone')->ignore($userId ,'user_id'),
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
