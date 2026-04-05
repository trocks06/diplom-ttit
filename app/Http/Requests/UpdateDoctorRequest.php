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
        return [
            'license' => ['sometimes', 'string', 'max:100', 'unique:doctors,license,' . $this->route('doctor')->id],
            'specialization_ids' => ['sometimes', 'array'],
            'specialization_ids.*' => ['integer', 'exists:specializations,id'],
        ];
    }
}
