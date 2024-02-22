<?php

namespace App\Http\Requests\Actions\Registration;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use App\Services\WalletService;
use Random\RandomException;

class GenerateOTP implements USSDMenu
{

    /**
     * menu
     *
     * This method handles the logic for generating and sending an OTP to the user's phone number.
     *
     * @param USSDRequest $request The USSD request object containing the user's information.
     * @param array $sessionData The session data array containing user's session information.
     *
     * @return array Returns an array with two keys: "continueSession" and "message".
     * - The "continueSession" key indicates whether the USSD session should continue or end.
     * - The "message" key contains the message to be displayed to the user.
     * @throws RandomException
     */
    public static function menu(USSDRequest $request, array $sessionData): array
    {
        $message = "Operation failed, please try again later.";

        if (WalletService::generateOTP($request->getMSISDN())) {
            updateSessionData($request->getSessionId(), 'otp');
            $message = "OTP has been sent to your phone number, please check and dial " . config('ussd.code') . " to continue your registration.";
        }

        return [
            "continueSession" => false,
            "message" => $message
        ];
    }
}
