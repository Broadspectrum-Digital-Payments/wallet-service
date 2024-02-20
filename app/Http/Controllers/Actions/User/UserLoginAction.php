<?php

namespace App\Http\Controllers\Actions\User;

use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\ControllerAction;
use App\Interfaces\HttpRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserLoginAction implements ControllerAction
{

    /**
     * @param UserLoginRequest|HttpRequest $request
     * @return mixed
     */
    public function handle(UserLoginRequest|HttpRequest $request): JsonResponse
    {
        $user = User::query()->where('phone_number', '=', $request->validated('phoneNumber'))->first();
        if (Hash::check($request->validated('pin'), $user->pin)) {
            $user->login();
            return successfulResponse(
                data: ["data" => new UserResource($user)], message: "Login successful"
            );
        }

        return errorResponse("Credential mismatched", ResponseAlias::HTTP_BAD_REQUEST);
    }
}
