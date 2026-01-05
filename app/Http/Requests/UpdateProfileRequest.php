<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    /**
     * الحصول على قواعد التحقق التي تنطبق على الطلب.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'mobile_number' => [
                'sometimes',
                'string',
                Rule::unique('users')->ignore($user->id)
            ],
            'birth_date' => ['sometimes', 'nullable', 'date'],
            'profile_image_path' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'identity_image_path' =>['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
