<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            "firstname" => ['required', 'string', 'max:100'],
            "lastname" => ['required', 'string', 'max:100'],
            "patronymic" => ['required', 'string', 'max:100'],
            "phone" => ['required', 'string', 'max:20'],
            "email" => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            "password" => ['required', 'string', 'min:8', 'max:50'],
            "avatar" => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ];
    }
}
