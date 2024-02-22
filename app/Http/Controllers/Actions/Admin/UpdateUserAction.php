<?php

namespace App\Http\Controllers\Actions\Admin;

use App\Http\Requests\UpdateUserRequest;
use App\Interfaces\HttpRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UpdateUserAction
{

    public function handle(HttpRequest|UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $user->update($request->validated());
            return successfulResponse(data: [], status: ResponseAlias::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            report($exception);
        }

        return errorResponse();
    }
}
