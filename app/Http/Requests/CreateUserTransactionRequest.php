<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserTransactionRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:100', 'max:1000000'],
            'account_number' => ['required', 'digits:10'],
            'account_issuer' => ['string', 'required'],
            'account_name' => ['sometimes', 'nullable'],
            'description' => ['string', 'required'],
            'type' => ['required', 'string', 'in:cash in,cash out,transfer,reversal,payment,remittance']
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'account_issuer' => $this->input('accountIssuer'),
            'account_number' => $this->input('accountNumber'),
            'account_name' => $this->input('accountName')
        ]);
    }
}
