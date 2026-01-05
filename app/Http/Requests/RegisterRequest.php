<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        //  من الواجهة الأولى
        'fullname' => ['required', 'string', 'max:255'],
        'phonenumber' => ['required', 'string', 'unique:users,phonenumber'],
        'password' => ['required', 'confirmed', Password::min(8)],
        'role' => ['required', Rule::in(['owner', 'renter'])],

        // من الواجهة الثانية
        'birth_date' => ['required', 'date'],
        'profile_image' => ['required', 'image', 'mimes:jpg,png', 'max:2048'],
        'id_image' => ['required', 'image', 'mimes:jpg,png', 'max:2048'],
    ];
}

}
