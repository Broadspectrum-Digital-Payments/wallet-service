<?php

namespace App\Observers;

use App\Events\UserBalanceUpdatedEvent;
use App\Models\Transaction;

class TransactionObserver
{

    public function creating(Transaction $transaction): void
    {
        $transaction->external_id = uuid_create();
        $transaction->fee = $transaction->fee ?? 0;
        $transaction->tax = $transaction->tax ?? 0;

        if ($transaction->isDebit() && $transaction->amount > 0) $transaction->amount = $transaction->amount * -1;

        if ($transaction->isCredit() && $transaction->amount < 0) $transaction->amount = $transaction->amount * -1;

        $transaction->balance_before = $transaction->user->available_balance;
        $transaction->balance_after = $transaction->balance_before + $transaction->amount;

        if ($transaction->isP2P()) $transaction->status = Transaction::COMPLETED;
    }
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $transaction->user->updateBalances();

        if ($transaction->isCompleted()) event(new UserBalanceUpdatedEvent(user: $transaction->user, transaction: $transaction));
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        info("Transaction updated", $transaction->getDirty());
        if ($transaction->isDirty(['status']) && in_array($transaction->status, ['completed', 'failed'])) {
            $transaction->user->updateBalances();
            event(new UserBalanceUpdatedEvent($transaction->user, $transaction));
        }

    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
