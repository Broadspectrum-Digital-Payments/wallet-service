<?php

namespace App\Http\Controllers\Actions\Transaction;

use App\Http\Resources\TransactionResource;
use App\Interfaces\ControllerAction;
use App\Interfaces\HttpRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TransactionAction
 *
 * This class represents an action that handles an HTTP request to retrieve a list of transactions.
 */
class UserTransactionsAction implements ControllerAction
{

    /**
     * Handles the given HTTP request.
     *
     * @param HttpRequest|Request $request The HTTP request object.
     * @param ?User $user (optional) The user object.
     * @return JsonResponse The JSON response.
     */
    public function handle(HttpRequest|Request $request, ?User $user = null): JsonResponse
    {
        try {
            $transactions = $user?->transactions()
                ->when($startDate = $request->query('startDate'), fn (Builder $query) => $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay()->toDateTimeString()))
                ->when($endDate = $request->query('endDate'), fn (Builder $query) => $query->where('created_at', '<=', Carbon::parse($endDate)->startOfDay()->toDateTimeString()))
                ->when($status = $request->query('status'), fn (Builder $query) => $query->where('status', '=', $status))
                ->when($type = $request->query('type'), fn (Builder $query) => $query->where('type', '=', $type))
                ->latest()
                ->paginate($pageSize = $request->query('pageSize', 50));

            return successfulResponse([
                'data' => TransactionResource::collection($transactions),
                'meta' => getPaginatedData($transactions, $pageSize)
            ]);
        } catch (Exception $exception) {
            report($exception);
        }

        return errorResponse();
    }
}
