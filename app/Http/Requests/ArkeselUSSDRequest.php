<?php

namespace App\Http\Requests;

use App\Interfaces\USSDRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ArkeselUSSDRequest extends FormRequest implements USSDRequest
{

    public function getSessionId()
    {
        return $this->validated('sessionID');
    }

    public function getUserId()
    {
        return $this->validated('userID');
    }

    public function getUserData()
    {
        return str_replace(config('ussd.code'), "", $this->validated('userData'));
    }

    public function getMSISDN(): string
    {
        return "233" . substr($this->validated('msisdn'), -9);
    }

    public function getNewSession()
    {
        return $this->validated('newSession');
    }

    public function getNetwork()
    {
        return $this->validated('network');
    }

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
            "sessionID" => ["required", "string"],
            "userID" => ["required", "string"],
            "newSession" => ["required", "bool"],
            "msisdn" => ["required", "string"],
            "userData" => ["required", "string"],
            "network" => ["required", "string"]
        ];
    }
}
