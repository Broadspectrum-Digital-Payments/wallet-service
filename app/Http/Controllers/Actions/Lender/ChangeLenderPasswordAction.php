<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions\Lender;

use Exception;
use App\Models\User;
use App\Interfaces\HttpRequest;
use Illuminate\Http\JsonResponse;
use App\Interfaces\ControllerAction;
use Psr\Container\ContainerExceptionInterface;
use App\Http\Requests\ChangeLenderPasswordRequest;
use App\Notifications\PasswordUpdatedNotification;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * Class ChangeLenderPasswordAction
 *
 * Handles the request for changing the lender password.
 */
class ChangeLenderPasswordAction implements ControllerAction
{
    /**
     * Handles the request for changing the lender password.
     *
     * @param HttpRequest|ChangeLenderPasswordRequest $request The HTTP request or ChangeLenderPasswordRequest object containing the necessary data.
     * @return JsonResponse The JSON response containing the result of the request.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(HttpRequest|ChangeLenderPasswordRequest $request): JsonResponse
    {
        try {
            if (checkOTP(phoneNumberToInternationalFormat($request->validated('phoneNumber')), $request->validated('otp'))) {
                $user = User::findByPhoneNumber($request->validated('phoneNumber'));
                $user->update(['password' => $request->validated('password')]);
                $user->notify(new PasswordUpdatedNotification);
                return successfulResponse([], "You have successfully updated your password.");
            }

            return errorResponse("OTP is wrong, please check and try again.", ResponseAlias::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            report($e);
        }

        return errorResponse();
    }
}
