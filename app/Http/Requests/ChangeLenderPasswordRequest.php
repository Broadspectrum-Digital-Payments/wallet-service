<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class ChangeLenderPasswordRequest extends FormRequest
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
            'phoneNumber' => ['required', 'digits:12', 'exists:users,phone_number'],
            'otp' => ['required', 'digits:6'],
            'password' => ['required', Password::defaults()],
            'passwordConfirmation' => ['required', 'same:password'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['phoneNumber' => phoneNumberToInternationalFormat($this->input('phoneNumber'))]);
    }
}
