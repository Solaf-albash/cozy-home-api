<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'apartment_id' => ['required', 'exists:apartments,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'number_of_persons' => ['required', 'integer', 'min:1', 'max:10'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'apartment_id.required' => 'يجب تحديد الشقة المراد حجزها.',
            'apartment_id.exists' => 'الشقة المحددة غير موجودة.',
            'start_date.required' => 'يجب تحديد تاريخ بداية الحجز.',
            'start_date.after_or_equal' => 'لا يمكن أن يكون تاريخ البداية في الماضي.',
            'end_date.required'   => 'يجب تحديد تاريخ نهاية الحجز.',
            'end_date.after_or_equal' => 'يجب أن يكون تاريخ النهاية بعد أو يساوي تاريخ البداية.',
        ];
    }
}
