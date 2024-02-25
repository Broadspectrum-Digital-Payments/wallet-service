<?php

namespace App\Http\Controllers\Actions\Transaction;

use Exception;
use App\Models\User;
use App\Interfaces\HttpRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use App\Interfaces\ControllerAction;
use Psr\Container\NotFoundExceptionInterface;
use App\Http\Requests\UserRegistrationRequest;
use Psr\Container\ContainerExceptionInterface;
use App\Notifications\UserRegisteredNotification;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LenderRegistrationAction implements ControllerAction
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
            $user = User::query()->create($request->validated());
            $user->refresh()->login();
            $user->notify(new UserRegisteredNotification);

            return successfulResponse(['data' => new UserResource($user)], 'User registered.', ResponseAlias::HTTP_CREATED);
        } catch (Exception $exception) {
            report($exception);
        }

        return errorResponse('Something went wrong, please try again later');
    }
}
