<?php

namespace App\Listeners;

use App\Events\UserBalanceUpdatedEvent;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\SuccessfulTransferNotification;

class UserBalanceUpdatedListener
{
    /**
     * Handle the UserBalanceUpdatedEvent.
     *
     * @param UserBalanceUpdatedEvent $event The event object.
     * @return void
     */
    public function handle(UserBalanceUpdatedEvent $event): void
    {
        $this->doActionBasedOnTransactionType($event->transaction, $event->user);
    }

    /**
     * Performs different actions based on the transaction type.
     *
     * @param Transaction $transaction The transaction object containing the necessary information for the actions.
     *        It should have the following properties:
     *        - isDebit: A boolean indicating whether the transaction is a debit or not.
     *        - isCredit: A boolean indicating whether the transaction is a credit or not.
     *        The transaction object should also have any other necessary properties based on the implementation
     *        of the performDebitActions and notify methods.
     *
     * @param User $user The user object associated with the transaction.
     *
     * @return void
     */
    private function doActionBasedOnTransactionType(Transaction $transaction, User $user): void
    {
        if ($transaction->isDebit()) $this->performDebitActions($transaction, $user);
        if ($transaction->isCredit()) $user->notify(new PaymentReceivedNotification($transaction));
    }

    /**
     * Performs the necessary debit actions for a transaction.
     *
     * @param Transaction $transaction The transaction object containing the necessary information for the debit.
     *        It should have the following properties:
     *        - user: User object representing the user associated with the debit.
     *        - amount: The amount to be debited.
     *        - account_number: The account number of the debit recipient.
     *        - description: A description of the debit.
     *        - stan: A unique identifier for the transaction.
     *        - isP2P: A boolean indicating whether the debit is peer-to-peer or not.
     * @param User $user The user associated with the debit.
     *
     * @return void
     */
    private function performDebitActions(Transaction $transaction, User $user): void
    {
        $user->notify(new SuccessfulTransferNotification($transaction));
        if ($transaction->isTransfer()) $this->transferAmountToUser($transaction);
    }

    /**
     * Transfers the specified amount to the user's account.
     *
     * @param Transaction $transaction The transaction object containing the necessary information for the transfer.
     *        It should have the following properties:
     *        - user: User object representing the user receiving the transfer.
     *        - amount: The amount to be transferred.
     *        - account_number: The account number of the recipient.
     *        - description: A description of the transfer.
     *        - stan: A unique identifier for the transaction.
     *        - isP2P: A boolean indicating whether the transfer is peer-to-peer or not.
     *
     * @return void
     */
    private function transferAmountToUser(Transaction $transaction): void
    {
        if ($transaction->status === Transaction::COMPLETED) {
            $transaction->user->transfer(
                $transaction->amount,
                $transaction->account_number,
                $transaction->description,
                $transaction->stan,
                $transaction->isP2P()
            );
        }
    }
}
