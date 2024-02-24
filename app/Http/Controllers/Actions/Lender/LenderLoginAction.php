<?php

namespace App\Http\Controllers\Actions\Lender;

use App\Models\User;
use App\Interfaces\HttpRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use App\Interfaces\ControllerAction;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LenderLoginAction implements ControllerAction
{

    /**
     * @param UserLoginRequest|HttpRequest $request
     * @return mixed
     */
    public function handle(UserLoginRequest|HttpRequest $request): JsonResponse
    {

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = User::query()
                ->where('email', $request->validated('email'))
                ->first();

            $user->login();
            return successfulResponse(
                data: ["data" => new UserResource($user->load('files'))],
                message: "Login successful"
            );
        }

        return errorResponse("Credential mismatched", ResponseAlias::HTTP_BAD_REQUEST);
    }
}
