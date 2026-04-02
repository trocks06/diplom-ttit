<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            "firstname" => ['sometimes', 'string', 'max:100'],
            "lastname" => ['sometimes', 'string', 'max:100'],
            "patronymic" => ['nullable', 'string', 'max:100'],
            "phone" => ['sometimes', 'string', 'max:20', 'unique:users,phone,' . $this->route('user')->id],
            "email" => ['sometimes', 'string', 'email', 'max:150', 'unique:users,email,' . $this->route('user')->id],
            "avatar" => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:2048'],
            "role_id" => ['sometimes', 'integer', 'exists:roles,id'],
            "verified" => ['sometimes', 'boolean'],
        ];
    }
}
