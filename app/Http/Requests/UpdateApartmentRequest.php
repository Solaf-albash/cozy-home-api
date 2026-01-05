<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApartmentRequest extends FormRequest
{

    public function authorize(): bool
    {
        // 1. نحصل على الشقة من المسار (Route)
        $apartment = $this->route('apartment');

        // 2. نتحقق: هل الـ ID للمستخدم الذي سجل دخوله
        //    هو نفسه الـ ID لمالك هذه الشقة؟
        return $this->user()->id === $apartment->owner_id;
    }
    public function rules(): array
    {
        return [
            'apartment_name' => ['sometimes', 'required', 'string', 'max:255'],
            'governorate' => ['sometimes', 'required', 'string'],
            'city' => ['sometimes', 'required', 'string'],
            'detailed_address' => ['sometimes', 'required', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'rent_type' => ['sometimes', 'required', Rule::in(['daily', 'monthly'])],
            'status' => ['sometimes', 'required', Rule::in(['available', 'unavailable'])],
        ];
    }
}
