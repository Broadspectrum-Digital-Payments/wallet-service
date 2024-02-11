<?php

namespace App\Http\Requests\Actions\Registration;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use App\Services\PaytabsWalletService;

class GenerateOTP implements USSDMenu
{

    public static function menu(USSDRequest $request, array $sessionData): array
    {
        $message = "Operation failed, please try again later.";

        if (PaytabsWalletService::generateOTP($request->getMSISDN())) {
            updateSessionData($request->getSessionId(), 'otp');
            $message = "OTP has been sent to your phone number, please check and dial " . config('ussd.code') . " to continue your registration.";
        }

        return [
            "continueSession" => false,
            "message" => $message
        ];
    }
}
