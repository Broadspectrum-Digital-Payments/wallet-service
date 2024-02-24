<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UserRegistrationRequest extends FormRequest
{
    private bool $isLender = false;

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
        return  [
            'name' => ['required', 'string'],
            'ghana_card_number' => ['required', 'string', 'min:10'],
            'phone_number' => ['required', 'digits:12', 'unique:users,phone_number'],
            'otp' => ['required', 'digits:6'],
            'type' => ['required', 'string'],
        ] + $this->getRequiredFeildByUserType();
    }

    public function messages(): array
    {
        return [
            'phone_number.digits' => 'Phone number must be 10 digits',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->isLender = str_contains($this->path(), 'lender');

        $this->merge([
            'ghana_card_number' => $this->input('ghanaCardNumber'),
            'phone_number' => phoneNumberToInternationalFormat($this->input('phoneNumber')),
            'type' => match (true) {
                str_contains($this->path(), 'agent') => 'agent',
                $this->isLender => 'lender',
                default => 'user'
            }
        ]);
    }

    private function getRequiredFeildByUserType()
    {
        return $this->isLender
            ? [
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', Password::defaults()],
                'passwordConfirmation' => ['required', 'same:password'],
            ] :
            [
                'pin' => ['required', 'digits:6'],
                'pinConfirmation' => ['required', 'digits:6', 'same:pin'],
            ];
    }
}
