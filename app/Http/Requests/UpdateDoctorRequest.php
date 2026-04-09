<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $doctor = $this->route('doctor');
        $userId = $doctor ? $doctor->user_id : null;
        $doctorId = $doctor ? $doctor->id : null;

        return [
            'firstname' => ['sometimes', 'required', 'string', 'max:100'],
            'lastname' => ['sometimes', 'required', 'string', 'max:100'],
            'patronymic' => ['nullable', 'string', 'max:100'],
            'phone' => ['sometimes', 'required', 'string', 'max:20', 'unique:users,phone,' . $userId],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:150', 'unique:users,email,' . $userId],
            'password' => ['nullable', 'string', 'min:8'],
            'license' => ['sometimes', 'required', 'string', 'max:100', 'unique:doctors,license,' . $doctorId],
            'specialization_ids' => ['sometimes', 'required', 'array', 'min:1'],
            'specialization_ids.*' => ['integer', 'exists:specializations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'license.unique' => 'Эта лицензия уже привязана к другому врачу.',
            'phone.unique' => 'Этот номер телефона уже зарегистрирован.',
            'email.unique' => 'Пользователь с таким email уже существует.',
            'specialization_ids.min'  => 'Необходимо указать хотя бы одну специализацию.',
            'specialization_ids.*.exists' => 'Выбранная специализация не найдена.',
        ];
    }
}
