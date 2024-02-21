<?php

namespace App\Listeners;

use App\Events\UserBalanceUpdatedEvent;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\SuccessfulTransferNotification;

class UserBalanceUpdatedListener
{
    /**
     * Handle the event.
     */
    public function handle(UserBalanceUpdatedEvent $event): void
    {
        $user = $event->user;
        $transaction = $event->transaction;

        if ($transaction->isDebit()) {
            $user->notify(new SuccessfulTransferNotification($transaction));
            if ($transaction->isTransfer()) $transaction->user->transfer(
                $transaction->amount,
                $transaction->account_number,
                $transaction->description,
                $transaction->stan,
                $transaction->isP2P()
            );
        }

        if ($transaction->isCredit()) $user->notify(new PaymentReceivedNotification($transaction));
    }
}
