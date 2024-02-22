<?php

namespace App\Http\Controllers\Actions\User;

use App\Http\Requests\ChangeUserPINRequest;
use App\Interfaces\ControllerAction;
use App\Interfaces\HttpRequest;
use App\Models\User;
use App\Notifications\PINUpdatedNotification;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * Class ChangeUserPINAction
 *
 * Handles the request for changing the user PIN.
 */
class ChangeUserPINAction implements ControllerAction
{
    /**
     * Handles the request for changing the user PIN.
     *
     * @param HttpRequest|ChangeUserPINRequest $request The HTTP request or ChangeUserPINRequest object containing the necessary data.
     * @return JsonResponse The JSON response containing the result of the request.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(HttpRequest|ChangeUserPINRequest $request): JsonResponse
    {
        try {
            if (checkOTP(phoneNumberToInternationalFormat($request->validated('phoneNumber')), $request->validated('otp'))) {
                $user = User::findByPhoneNumber($request->validated('phoneNumber'));
                $user->update(['pin' => $request->validated('pin')]);
                $user->notify(new PINUpdatedNotification);
                return successfulResponse([], "You have successfully updated your PIN.");
            }

            return errorResponse("OTP is wrong, please check and try again.", ResponseAlias::HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            report($e);
        }

        return errorResponse();
    }
}
