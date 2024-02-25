<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'status' => ['sometimes', 'in:activated,deactivated'],
            'kyc_status' => ['sometimes', 'in:approved,declined']
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('kycStatus')) {
            $this->merge([
                'kyc_status' => $this->input('kycStatus')
            ]);
        }
    }
}
