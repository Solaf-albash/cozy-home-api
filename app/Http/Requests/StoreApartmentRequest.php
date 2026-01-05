<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // فقط المستخدم الذي صلاحيته 'owner' يمكنه إنشاء شقة
        return $this->user()->role === 'owner';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
public function rules(): array
{
    return [
        'apartment_name' => ['required', 'string', 'max:255'],
        'governorate' => ['required', 'string'],
        'city' => ['required', 'string'],
        'detailed_address' => ['required', 'string'],
        'price' => ['required', 'numeric', 'min:0'],
        'rent_type' => ['required', Rule::in(['daily', 'monthly'])],
        'images' => ['required', 'array', 'min:1', 'max:10'],
        'images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
    ];
}

}
