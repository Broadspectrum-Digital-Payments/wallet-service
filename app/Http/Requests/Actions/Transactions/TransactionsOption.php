<?php

namespace App\Http\Requests\Actions\Transactions;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use App\Models\User;
use Psr\SimpleCache\InvalidArgumentException;

class TransactionsOption implements USSDMenu
{

    /**
     * @throws InvalidArgumentException
     */
    public static function menu(USSDRequest $request, array $sessionData): array
    {
        if (count($sessionData) === 2) {
            $validatePIN = validatePIN($request->getMSISDN(), last($sessionData));
            if ($validatePIN->fails()) {
                clearSessionData($request->getSessionId());
                return endedSessionMessage($validatePIN->messages()->first());
            }

            $user = User::findByPhoneNumber($request->getMSISDN());

            $transactions = $user->transactions()->latest()->take(4)->get();

            $transactionsToArray = $transactions->map(fn ($transaction, $i) => $i + 1 . ". {$transaction->created_at->format('Y-m-d')} {$transaction->type} GHS {$transaction->getAmountInMajorUnits()}");

            clearSessionData($request->getSessionId());
            return endedSessionMessage(ussdMenu([
                (!$transactions->count()) ? "No transactions found." : "Transactions", ...$transactionsToArray
            ]));
        }

        return continueSessionMessage(ussdMenu([
            "Transactions",
            "Enter your 6 digit PIN:"
        ]));
    }
}
