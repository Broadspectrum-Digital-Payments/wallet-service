<?php

namespace App\Http\Requests\Actions\Registration;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use App\Services\WalletService;
use Illuminate\Support\Facades\Validator;

class EnterGhanaCardNumber implements USSDMenu
{

    public static function menu(USSDRequest $request, array $sessionData): array
    {
        $validation = self::validateRequest($sessionData[2]);

        if ($validation->fails()) return endedSessionMessage($validation->messages()->first());

        return continueSessionMessage("Enter Ghana card number");
    }

    /**
     * @param $sessionData
     * @return \Illuminate\Validation\Validator
     */
    public static function validateRequest($sessionData): \Illuminate\Validation\Validator
    {
        return Validator::make([
            'otp' => $sessionData
        ], [
            'otp' => ['required', 'digits:6',]
        ], [
            'digits_between' => "OTP must be 6 digits long, please try again."
        ]);
    }
}
