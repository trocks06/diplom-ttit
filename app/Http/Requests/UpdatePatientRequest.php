<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $patient = $this->route('patient');
        $userId = $patient ? $patient->user_id : null;

        return [
            'firstname'  => ['sometimes', 'required', 'string', 'max:100'],
            'lastname'   => ['sometimes', 'required', 'string', 'max:100'],
            'patronymic' => ['nullable', 'string', 'max:100'],
            'phone'      => ['sometimes', 'required', 'string', 'max:20', 'unique:users,phone,' . $userId],
            'email'      => ['sometimes', 'required', 'string', 'email', 'max:150', 'unique:users,email,' . $userId],
            'password'   => ['nullable', 'string', 'min:8'],

            'address'          => ['sometimes', 'required', 'string', 'max:255'],
            'gender'           => ['sometimes', 'required', 'string', 'in:Мужской,Женский'],
            'allergies'        => ['nullable', 'string', 'max:500'],
            'chronic_diseases' => ['nullable', 'string', 'max:500'],
            'birth_date'       => ['sometimes', 'required', 'date', 'date_format:d.m.Y', 'before:today'],
        ];
    }
}
