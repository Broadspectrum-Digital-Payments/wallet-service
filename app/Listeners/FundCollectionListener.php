<?php

namespace App\Listeners;

use App\Events\FundCollectionEvent;
use App\Services\MiddlewareService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FundCollectionListener
{
    /**
     * Handle the event.
     */
    public function handle(FundCollectionEvent $event): void
    {
        $transaction = $event->transaction;

        $response = MiddlewareService::collect($transaction->account_number, strtolower($transaction->account_issuer), $transaction->stan, $transaction->amount, $transaction->description);
        if (!empty($response)) {
            $transaction->update(['status' => 'initiated', 'processor_reference' => $response['data']['stan']]);
        }
    }
}
