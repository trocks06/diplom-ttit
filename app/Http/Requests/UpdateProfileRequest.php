<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
// UpdateProfileRequest.php
    public function rules(): array
    {
        $userId = auth()->id();
        return [
            'firstname' => ['sometimes', 'string', 'max:100'],
            'lastname'  => ['sometimes', 'string', 'max:100'],
            'patronymic'=> ['nullable', 'string', 'max:100'],
            'phone'     => ['sometimes', 'string', 'max:20', 'unique:users,phone,' . $userId],
            'email'     => ['sometimes', 'string', 'email', 'max:150', 'unique:users,email,' . $userId],
            'address'   => ['sometimes', 'string', 'max:255'],
            'gender'    => ['sometimes', 'string', 'in:Мужской,Женский'],
            'allergies' => ['nullable', 'string', 'max:500'],
            'chronic_diseases' => ['nullable', 'string', 'max:500'],
            'birth_date'=> ['sometimes', 'date', 'date_format:d.m.Y', 'before:today'],
        ];
    }
}
