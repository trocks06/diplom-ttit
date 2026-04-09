<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => [
                'sometimes',
                'date',
                'date_format:d.m.Y H:i',
                'after:now'
            ],
            'end_time' => [
                'sometimes',
                'date',
                'date_format:d.m.Y H:i',
                'after:start_time'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.date_format' => 'Формат даты должен быть: дд.мм.гггг чч:мм (например, 05.12.2026 12:00).',
            'start_time.after' => 'Нельзя назначить прием на прошедшее время.',
            'end_time.after' => 'Время окончания должно быть позже времени начала.',
        ];
    }
}
