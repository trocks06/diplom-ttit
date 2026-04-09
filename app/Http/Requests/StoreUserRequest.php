<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "firstname" => ['required', 'string', 'max:100'],
            "lastname" => ['required', 'string', 'max:100'],
            "patronymic" => ['nullable', 'string', 'max:100'],
            "phone" => ['required', 'string', 'max:20', 'unique:users,phone'],
            "email" => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            "password" => ['required', 'string', 'min:8'],
        ];
    }
}
