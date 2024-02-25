<?php

namespace App\Http\Requests;

use App\Interfaces\HttpRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UserLoginRequest extends FormRequest implements HttpRequest
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
        return $this->isLender
            ? [
                'email' => ['required', 'email', 'exists:users,email'],
                'password' => ['required', 'string'],
            ]
            : [
                'pin' => ['required', 'digits:6'],
                'phoneNumber' => ['required', 'digits:12', 'exists:users,phone_number']
            ];
    }

    public function messages(): array
    {
        return [
            'phoneNumber.digits' => 'Phone number must be 10 digits'
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!($this->isLender = str_contains($this->path(), 'lender'))) {
            $this->merge(['phoneNumber' => phoneNumberToInternationalFormat($this->input('phoneNumber'))]);
        }
    }
}
