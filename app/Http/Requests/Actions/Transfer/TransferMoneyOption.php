<?php

namespace App\Http\Requests\Actions\Transfer;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Psr\SimpleCache\InvalidArgumentException;

class TransferMoneyOption implements USSDMenu
{

    /**
     * @throws InvalidArgumentException
     */
    public static function menu(USSDRequest $request, array $sessionData): array
    {
        info($sessionData);
        $amount = $sessionData[3] ?? 0;
        $user = User::findByPhoneNumber($request->getMSISDN());

        if (isset($sessionData[5])) {
            return match ($sessionData[4]) {
                '1' => self::transferFunds($user, (float)$amount * 100, $sessionData[2], 'P2P Transfer', 'gmo', $request->getSessionId()),
                '2' => self::cancelTransaction($request->getSessionId()),
                default => unknownOptionMessage()
            };
        }

        if (isset($sessionData[4])) {
            if ($sessionData[4] === '2') {
                return self::cancelTransaction($request->getSessionId());
            }

            $pin = $sessionData[4];

            if (!Hash::check($pin, $user->pin)) {
                return endedSessionMessage('Wrong PIN, please check and try again.');
            }

            return self::transferFunds($user, (float)$amount * 100, $sessionData[2], 'P2P Transfer', 'gmo', $request->getSessionId());
        }

        if (isset($sessionData[3])) {
            if ($user->available_balance < ((float)$amount * 100)) {
                clearSessionData($request->getSessionId());
                return endedSessionMessage("You don't have enough funds to perform this transactions");
            }

            return continueSessionMessage(\ussdMenu([
                'Confirm transfer of GHS ' . $sessionData[3] . ' to G-Money account: ' . $sessionData[2] . ' [Account Name]' . ' fee: 0.0',
                'Enter PIN to confirm or 2 to cancel',
            ]));
        }


        if (isset($sessionData[2])) {
            $user = User::findByPhoneNumber(phoneNumberToInternationalFormat($sessionData[2]));
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

        if (isset($sessionData[1])) {
            return continueSessionMessage(\ussdMenu([
                'G-Money',
                'Enter account number'
            ]));
        }

        return continueSessionMessage(ussdMenu([
            "Transfer Money",
            "1. G-Money",
//            "2. MTN",
//            "3. Vodafone",
//            "4. AT Money",
//            "5. Bank"
        ]));
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function transferFunds(User $user, int $amount, string $accountNumber, string $description, string $accountIssuer, string $sessionId): array
    {
        $transaction = $user->transactions()->create([
            'amount' => $amount,
            'account_number' => $accountNumber,
            'account_issuer' => $accountIssuer,
            'description' => $description,
            'stan' => generateStan(),
            'type' => Transaction::TRANSFER
        ]);

        if ($transaction) {
            $user = $user->refresh();
            clearSessionData($sessionId);
            return endedSessionMessage("You have transferred GHS {$transaction->getAmountInMajorUnits()} to {$transaction->account_number}. Your actual balance is GHS {$user->getActualBalanceInMajorUnits()} and available balance is GHS {$user->getAvailableBalanceInMajorUnits()}.");
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
