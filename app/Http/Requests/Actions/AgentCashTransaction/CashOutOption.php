<?php

declare(strict_types=1);

namespace App\Http\Requests\Actions\AgentCashTransaction;

use App\Models\User;
use App\Models\Transaction;
use App\Interfaces\USSDRequest;

final class CashOutOption
{
    const ACCT_NO = 1;
    const AMOUNT = 2;
    const CONFIRM = 3;
    const CANCEL = '2';

    /**
     * @throws InvalidArgumentException
     */
    public static function menu(USSDRequest $request, array $sessionData): array
    {
        info($sessionData);

        $amount = $sessionData[self::AMOUNT] ?? 0;

        if (isset($sessionData[self::CONFIRM])) {
            if ($sessionData[self::CONFIRM] === self::CANCEL) {
                return self::cancelTransaction($request->getSessionId());
            }

            if (intval($sessionData[self::CONFIRM]) != 1) {
                clearSessionData($request->getSessionId());
                return endedSessionMessage('Wrong input, please check and try again.');
            }

            return self::initiateFundsRequest(self::customer($sessionData[self::ACCT_NO]), (float)$amount * 100, $request->getMSISDN(), 'P2P Transfer', 'gmo', $request->getSessionId());
        }

        if (isset($sessionData[self::AMOUNT])) {
            if ((self::customer($sessionData[self::ACCT_NO]))->available_balance < ((float)$amount * 100)) {
                clearSessionData($request->getSessionId());
                return endedSessionMessage("Customer does not have enough funds for this transactions");
            }

            return continueSessionMessage(\ussdMenu([
                'You are requesting a credit of GHS ' . $sessionData[2] . ' from G-Money account: ' . $sessionData[1] . ' [Account Name].',
                '',
                'Enter 1 to confirm or 2 to cancel',
            ]));
        }

        if (isset($sessionData[self::ACCT_NO])) {
            $user = self::customer($sessionData[self::ACCT_NO]);

            if (!$user) {
                clearSessionData($request->getSessionId());
                return endedSessionMessage('Invalid account number, please check and try again.');
            }

            if ($user->phone_number === $request->getMSISDN()) {
                clearSessionData($request->getSessionId());
                return endedSessionMessage('You cannot cash out from your account.');
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
    private static function initiateFundsRequest(User $customer, float $amount, string $accountNumber, string $description, string $accountIssuer, string $sessionId): array
    {
        $transaction = $customer->transactions()->create([
            'amount' => $amount,
            'account_number' => $accountNumber,
            'account_issuer' => $accountIssuer,
            'description' => $description,
            'stan' => generateStan(),
            'type' => Transaction::TRANSFER
        ]);

        clearSessionData($sessionId);

        if ($transaction) {
            $phone = phoneNumberToLocalFormat($customer->phone_number);
            return endedSessionMessage("An approval message has been sent to $phone, please await a success message.");
        }

        return endedSessionMessage('Cash out failed, please try again later.');
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function cancelTransaction(string $sessionId): array
    {
        clearSessionData($sessionId);
        return endedSessionMessage('Transaction cancelled by user.');
    }

    public static function customer(string $phoneNumber)
    {
        return User::findByPhoneNumber(phoneNumberToInternationalFormat($phoneNumber));
    }
}
