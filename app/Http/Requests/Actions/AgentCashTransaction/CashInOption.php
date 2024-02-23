<?php

declare(strict_types=1);

namespace App\Http\Requests\Actions\AgentCashTransaction;

use App\Models\User;
use App\Models\Transaction;
use App\Interfaces\USSDRequest;
use Illuminate\Support\Facades\Hash;

final class CashInOption
{
    const ACCT_NO = 1;
    const AMOUNT = 2;
    const PIN = 3;
    const CANCEL = '2';

    /**
     * @throws InvalidArgumentException
     */
    public static function menu(USSDRequest $request, array $sessionData): array
    {
        info($sessionData);

        $amount = $sessionData[self::AMOUNT] ?? 0;
        $user = User::findByPhoneNumber($request->getMSISDN());

        if (isset($sessionData[self::PIN])) {
            if ($sessionData[self::PIN] === self::CANCEL) {
                return self::cancelTransaction($request->getSessionId());
            }

            $pin = $sessionData[self::PIN];

            if (!Hash::check($pin, $user->pin)) {
                clearSessionData($request->getSessionId());
                return endedSessionMessage('Wrong PIN, please check and try again.');
            }

            return self::depositFunds($user, (float)$amount * 100, $sessionData[self::ACCT_NO], 'P2P Transfer', 'gmo', $request->getSessionId());
        }

        if (isset($sessionData[self::AMOUNT])) {
            if ($user->available_balance < ((float)$amount * 100)) {
                clearSessionData($request->getSessionId());
                return endedSessionMessage("You don't have enough funds to perform this transactions");
            }

            return continueSessionMessage(\ussdMenu([
                'Confirm deposit of GHS ' . $sessionData[self::AMOUNT] . ' from G-Money account: ' . $sessionData[self::ACCT_NO] . ' [Account Name]' . ' fee: 0.0',
                '',
                'Enter PIN to confirm or 2 to cancel',
            ]));
        }

        if (isset($sessionData[self::ACCT_NO])) {
            $user = User::findByPhoneNumber(phoneNumberToInternationalFormat($sessionData[1]));
            if (!$user) {
                clearSessionData($request->getSessionId());
                return endedSessionMessage('Invalid account number, please check and try again.');
            }

            if ($user->phone_number === $request->getMSISDN()) {
                clearSessionData($request->getSessionId());
                return endedSessionMessage('Sender and recipient account cannot be the same.');
            }

            return continueSessionMessage(\ussdMenu([
                'G-Money',
                'Enter amount'
            ]));
        }

        return continueSessionMessage(\ussdMenu([
            'G-Money',
            'Enter account number'
        ]));
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function depositFunds(User $user, int $amount, string $accountNumber, string $description, string $accountIssuer, string $sessionId): array
    {
        $transaction = $user->transactions()->create([
            'amount' => $amount,
            'account_number' => $accountNumber,
            'account_issuer' => $accountIssuer,
            'description' => $description,
            'stan' => generateStan(),
            'type' => Transaction::TRANSFER
        ]);

        clearSessionData($sessionId);

        if ($transaction) {
            $user = $user->refresh();
            return endedSessionMessage("You have deposited GHS {$transaction->getAmountInMajorUnits()} into {$transaction->account_number}. Your actual balance is GHS {$user->getActualBalanceInMajorUnits()} and available balance is GHS {$user->getAvailableBalanceInMajorUnits()}.");
        }

        return endedSessionMessage('Transfer failed, please try again later.');
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function cancelTransaction(string $sessionId): array
    {
        clearSessionData($sessionId);
        return endedSessionMessage('Transaction cancelled by user.');
    }
}
