<?php

namespace App\Http\Controllers\Actions\User;

use App\Http\Requests\SendUserOTPRequest;
use App\Interfaces\ControllerAction;
use App\Interfaces\HttpRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class UserOTPAction implements ControllerAction
{
    public function handle(HttpRequest|SendUserOTPRequest $request): JsonResponse
    {
        try {
            info("User OTP Action");
            sendOTP($request->validated('phoneNumber'));
            return successfulResponse([], "OTP sent to " . $request->validated('phoneNumber'));
        } catch (Exception $e) {
            report($e);
            return errorResponse("Failed to send OTP, please try again later.");
        }
    }
}
