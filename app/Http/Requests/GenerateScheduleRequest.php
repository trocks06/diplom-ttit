<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id'      => 'required|exists:doctors,id',
            'start_date'     => 'required|date',
            'days'           => 'required|string',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i',
            'slot_duration'  => 'required|integer|min:5',
            'breaks'         => 'nullable|string',
            'weeks'          => 'nullable|integer|min:1|max:4',
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_id.required' => 'Пожалуйста, выберите врача.',
            'doctor_id.exists'   => 'Выбранный врач не найден в системе.',
            'start_date.required' => 'Укажите дату начала генерации расписания.',
            'start_date.date'     => 'Дата начала должна быть в формате ГГГГ-ММ-ДД.',
            'days.required' => 'Укажите дни недели через запятую (например: 1,2,3,4,5).',
            'days.string'   => 'Дни недели должны быть строкой.',
            'start_time.required'    => 'Укажите время начала рабочего дня.',
            'start_time.date_format' => 'Время начала должно быть в формате ЧЧ:ММ (например, 09:00).',
            'end_time.required'    => 'Укажите время окончания рабочего дня.',
            'end_time.date_format' => 'Время окончания должно быть в формате ЧЧ:ММ (например, 18:00).',
            'slot_duration.required' => 'Укажите длительность одного слота (в минутах).',
            'slot_duration.integer'  => 'Длительность слота должна быть целым числом.',
            'slot_duration.min'      => 'Минимальная длительность слота — 5 минут.',
            'breaks.string' => 'Перерывы должны быть указаны строкой (например, 13:00-14:00,15:30-16:00).',
            'weeks.integer' => 'Количество недель должно быть целым числом.',
            'weeks.min'     => 'Минимальное количество недель — 1.',
            'weeks.max'     => 'Максимальное количество недель — 4.',
        ];
    }
}
