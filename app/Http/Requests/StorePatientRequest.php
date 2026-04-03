<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'address' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:Мужской,Женский'],
            'allergies' => ['nullable', 'string', 'max:500'],
            'chronic_diseases' => ['nullable', 'string', 'max:500'],
            'birth_date' => ['required', 'date', 'date_format:d.m.Y', 'before:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Необходимо указать ID пользователя.',
            'user_id.exists' => 'Пользователь с таким ID не существует.',
            'gender.in' => 'Пол может быть: Мужской, Женский.',
            'birth_date.date' => 'Дата рождения должна быть корректной датой и соответствовать формату: дд.мм.гггг.',
            'birth_date.before' => 'Дата рождения не может быть позже сегодняшнего дня.',
        ];
    }
}
