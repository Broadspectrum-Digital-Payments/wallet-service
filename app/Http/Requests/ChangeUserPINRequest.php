<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangeUserPINRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'phoneNumber' => ['required', 'digits:10', 'exists:users,phone_number'],
            'otp' => ['required', 'digits:6'],
            'pin' => ['required', 'digits:4'],
            'pinConfirmation' => ['required', 'digits:4', 'same:pin'],
        ];
    }
}
