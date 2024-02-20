<?php

namespace App\Http\Controllers\Actions\User;

use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\ControllerAction;
use App\Interfaces\HttpRequest;
use App\Models\User;
use App\Services\HubtelSMSService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserRegistrationAction implements ControllerAction
{

    public function handle(UserRegistrationRequest|HttpRequest $request): JsonResponse
    {
        try {
            if (($cachedOTP = cache()->get($request->validated('phone_number') . 'otp')) && $cachedOTP == $request->validated('otp')) {
                $user = User::query()->create($request->validated());
                $user->refresh()->login();
                HubtelSMSService::send($request->validated('phone_number'), "Hi, your BSL wallet has been created successfully. Please complete your KYC on our mobile app with your Ghana card, regards.");
                return successfulResponse(['data' => new UserResource($user)], 'User registered.', ResponseAlias::HTTP_CREATED);
            }

            return errorResponse("OTP verification failed, please generate a new one and try again.", ResponseAlias::HTTP_BAD_REQUEST);

        } catch (Exception $exception) {
            report($exception);
        }

        return errorResponse('Something went wrong, please try again later');
    }
}
