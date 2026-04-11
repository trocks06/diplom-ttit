<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'file' => ['sometimes', 'file', 'mimes:jpg,png,pdf,docx', 'max:10240'],
            'file_name' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
