<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\Actions\Admin\UserIndexAction;
use App\Http\Controllers\Actions\Admin\UpdateUserAction;
use App\Http\Controllers\Actions\Transaction\TransactionIndexAction;

class AdminUserController extends Controller
{
    public function index(Request $request, UserIndexAction $action)
    {
        return $action->handle($request);
    }

    public function show(User $user)
    {
        return successfulResponse([
            'data' => new UserResource($user->load('files'))
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action)
    {
        return $action->handle($request, $user);
    }

    public function transactons(User $user, Request $request, TransactionIndexAction $action)
    {
        return $action->handle($request, user: $user);
    }
}
