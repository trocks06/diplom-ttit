<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
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
            'license' => ['required', 'string', 'max:100', 'unique:doctors,license'],
            'specialization_ids' => ['required', 'array', 'min:1'],
            'specialization_ids.*' => ['integer', 'exists:specializations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'Этот номер телефона уже зарегистрирован.',
            'email.unique' => 'Пользователь с таким email уже существует.',
            'license.unique' => 'Врач с таким номером лицензии уже существует в системе.',
            'specialization_ids.min'  => 'Необходимо указать хотя бы одну специализацию.',
            'specialization_ids.*.exists' => 'Выбранная специализация не найдена.',
        ];
    }
}
