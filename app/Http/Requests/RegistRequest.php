<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegistRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',      
                'regex:/[A-Z]/',      
                'regex:/[0-9]/',      
            ],
            'name' => 'string|required|max:45',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Az e-mail cím megadása kötelező.',
            'email.email' => 'Az e-mail cím nem érvényes.',
            'email.unique' => 'Az e-mail cím már használatban van.',
            
            'password.required' => 'A jelszó megadása kötelező.',
            'password.min' => 'A jelszónak legalább 8 karakter hosszúnak kell lennie.',
            'password.regex' => 'A jelszónak tartalmaznia kell kisbetűt, nagybetűt és számot.',
 
            'name.string' => 'Érvénytelen felhasználónév.',
            'name.required' => 'A felhasználónév megadása kötelező.',
            'name.max' => 'Túl hosszú felhasználónév.',
        ];
    }
    


    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Hiba történt a validálás során!',
            'errors' => $validator->errors(),
        ], 422));
    }
}
