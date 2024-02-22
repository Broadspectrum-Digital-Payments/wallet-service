<?php

namespace App\Http\Controllers\Actions\User;

use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\ControllerAction;
use App\Interfaces\HttpRequest;
use App\Models\User;
use App\Notifications\UserRegisteredNotification;
use Exception;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserRegistrationAction implements ControllerAction
{

    /**
     * Handle the user registration request.
     *
     * @param UserRegistrationRequest|HttpRequest $request The user registration request or HTTP request.
     * @return JsonResponse The JSON response with the registration status.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(UserRegistrationRequest|HttpRequest $request): JsonResponse
    {
        try {
            if (checkOTP(phoneNumberToInternationalFormat($request->validated('phone_number')), $request->validated('otp'))) {
                $user = User::query()->create($request->validated());
                $user->refresh()->login();
                $user->notify(new UserRegisteredNotification);
                return successfulResponse(['data' => new UserResource($user)], 'User registered.', ResponseAlias::HTTP_CREATED);
            }

            return errorResponse("OTP verification failed, please generate a new one and try again.", ResponseAlias::HTTP_BAD_REQUEST);

        } catch (Exception $exception) {
            report($exception);
        }

        return errorResponse('Something went wrong, please try again later');
    }
}
