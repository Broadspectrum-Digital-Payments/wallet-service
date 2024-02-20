<?php

namespace App\Http\Requests\Actions\Registration;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use App\Services\WalletService;
use Illuminate\Support\Facades\Validator;

class VerifyOTP implements USSDMenu
{

    public static function menu(USSDRequest $request, array $sessionData): array
    {
        info("otp", [$sessionData[2]]);
        $validation = self::validateRequest($otp = $sessionData[2]);

        if ($validation->fails()) return endedSessionMessage($validation->messages()->first());

        if ($response = WalletService::verifyOTP($request->getMSISDN(), $otp)) {
            if (($response['status'] ?? null) && $resourceId = $response['payloadX']['resourceId'] ?? null) {
                updateSessionData($request->getSessionId(), $resourceId);
                return EnterFullNameOption::menu($request, $sessionData);
            }

            return endedSessionMessage("OTP verification failed, please try again.");
        }

        return operationFailedMessage();
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
