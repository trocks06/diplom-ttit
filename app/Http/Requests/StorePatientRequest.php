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
            'firstname' => ['required', 'string', 'max:100'],
            'lastname' => ['required', 'string', 'max:100'],
            'patronymic' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
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
            'gender.in'         => 'Пол может быть: Мужской, Женский.',
            'birth_date.date'   => 'Укажите корректную дату.',
            'birth_date.before' => 'Дата рождения не может быть в будущем.',
            'phone.unique'      => 'Этот номер телефона уже зарегистрирован.',
            'email.unique'      => 'Пользователь с таким email уже существует.',
        ];
    }
}
