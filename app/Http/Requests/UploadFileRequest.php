<?php

namespace App\Http\Requests;

use App\Interfaces\HttpRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest implements HttpRequest
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
            'selfie' => ['sometimes', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:2000'],
            'ghana-card-front' => ['sometimes', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:2000'],
            'ghana-card-back' => ['sometimes', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:2000'],
        ];
    }
}
