<?php

namespace App\Http\Controllers\Actions\User;

use App\Http\Requests\SendUserOTPRequest;
use App\Interfaces\ControllerAction;
use App\Interfaces\HttpRequest;
use App\Services\HubtelSMSService;
use Exception;
use Illuminate\Http\JsonResponse;

class UserOTPAction implements ControllerAction
{

    public function handle(HttpRequest|SendUserOTPRequest $request): JsonResponse
    {
        try {
            $otp = random_int(100000, 999999);
            $phoneNumber = $request->validated('phoneNumber');

            cache()->put($phoneNumber . "otp", $otp, now()->addMinutes(3));

            $response = HubtelSMSService::send($phoneNumber, "Your BSL wallet verification code is: " . $otp);

            if (!empty($response) && $response['messageId'] ?? null) {
                return successfulResponse([], "OTP sent to " . $phoneNumber);
            }

            return errorResponse("Failed to send OTP, please try again later.");
        } catch (Exception $e) {
            report($e);
            return errorResponse();
        }
    }
}
