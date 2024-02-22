<?php

namespace App\Http\Controllers\Actions\Admin;

use App\Http\Resources\UserResource;
use App\Interfaces\ControllerAction;
use App\Interfaces\HttpRequest;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserIndexAction implements ControllerAction
{

    public function handle(HttpRequest|Request $request): JsonResponse
    {
        try {
            $users = User::query()
                ->orderBy('name')
                ->when($status = $request->query('status'), fn(Builder $query) => $query->where('status', '=', $status))
                ->when($kycStatus = $request->query('kyc_status'), fn(Builder $query) => $query->where('kyc_status', '=', $kycStatus))
                ->when($type = $request->query('type'), fn(Builder $query) => $query->where('type', '=', $type))
                ->when($name = $request->query('name'), fn(Builder $query) => $query->where('name', 'LIKE', "%$name%"))
                ->when($phone = $request->query('phoneNumber'), fn(Builder $query) => $query->where('phone_number', 'LIKE', "%$phone%"))
                ->paginate($pageSize = $request->query('pageSize', 50));

            return successfulResponse([
                'data' => [
                    'users' => UserResource::collection($users),
                    'meta' => getPaginatedData($users, $pageSize)
                ],
            ], "Users retrieved");
        } catch (Exception $exception) {
            report($exception);
        }

        return errorResponse();
    }
}
