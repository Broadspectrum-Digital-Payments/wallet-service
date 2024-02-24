<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Actions\Transaction\CreateTransactionAction;
use App\Http\Controllers\Actions\Transaction\TransactionIndexAction;
use App\Http\Requests\CreateUserTransactionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserTransactionController extends Controller
{
    public function index(Request $request, TransactionIndexAction $action): JsonResponse
    {
        return $action->handle($request, $request->user());
    }

    public function store(CreateUserTransactionRequest $request, CreateTransactionAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
