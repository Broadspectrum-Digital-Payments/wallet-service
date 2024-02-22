<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Actions\Transaction\TransactionIndexAction;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    public function index(Request $request, TransactionIndexAction $action)
    {
        return $action->handle($request);
    }

    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction->load('user'));
    }
}
