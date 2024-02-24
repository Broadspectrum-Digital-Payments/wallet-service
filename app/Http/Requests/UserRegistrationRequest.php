<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRegistrationRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'ghana_card_number' => ['required', 'string', 'min:10'],
            'phone_number' => ['required', 'digits:12', 'unique:users,phone_number'],
            'pin' => ['required', 'digits:6'],
            'otp' => ['required', 'digits:6'],
            'pinConfirmation' => ['required', 'digits:6', 'same:pin'],
            'type' => ['required', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.digits' => 'Phone number must be 10 digits'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ghana_card_number' => $this->input('ghanaCardNumber'),
            'phone_number' => phoneNumberToInternationalFormat($this->input('phoneNumber')),
            'type' => str_contains($this->path(), 'agent') ? 'agent' : 'user'
        ]);
    }
}
