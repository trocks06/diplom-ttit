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
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:doctors,user_id'],
            'license' => ['required', 'string', 'max:100', 'unique:doctors,license'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Необходимо указать пользователя.',
            'user_id.exists' => 'Пользователь не найден.',
            'user_id.unique' => 'Этот пользователь уже является доктором.',
            'license.unique' => 'Лицензия с таким номером уже существует.',
        ];
    }
}
