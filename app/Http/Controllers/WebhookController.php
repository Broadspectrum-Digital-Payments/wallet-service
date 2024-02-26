<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function middlewareCallback(Request $request)
    {
        info('Middleware Callback Request', $request->input());
        if ($data = $request->input('data')) {
            if (($transaction = Transaction::query()->where('stan', '=', $data['reference'])->first()) && $data['status'] <> 'pending') {
                $transaction->update(['status' => $data['status'] === 'success' ? 'completed' : 'failed']);
            }
        }
    }
}
