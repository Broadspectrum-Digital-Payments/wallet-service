<?php

namespace App\Http\Controllers\Actions\User;

use App\Http\Requests\VerifyUserOTPRequest;
use App\Interfaces\ControllerAction;
use App\Interfaces\HttpRequest;
use App\Services\HubtelSMSService;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserOTPVerificationAction implements ControllerAction
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function handle(HttpRequest|VerifyUserOTPRequest $request): JsonResponse
    {
        try {
            $phoneNumber = $request->validated('phoneNumber');
            $otp = $request->validated('otp');

            if ($cachedOTP = getCachedOTP($phoneNumber)) {
                if ($cachedOTP == $otp) {
                    cache()->delete($phoneNumber . 'otp');
                    HubtelSMSService::send($phoneNumber, "You have successfully verified your phone number.");
                    return successfulResponse([], "User OTP verified successfully.");
                }

                return errorResponse("OTP is wrong, please try again.", ResponseAlias::HTTP_BAD_REQUEST);
            }

            return errorResponse("OTP has expired, please generate a new one.", ResponseAlias::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            report($exception);
        }

        return errorResponse();
    }
}
