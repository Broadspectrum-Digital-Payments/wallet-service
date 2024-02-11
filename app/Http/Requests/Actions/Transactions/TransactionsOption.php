<?php

namespace App\Http\Requests\Actions\Transactions;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;

class TransactionsOption implements USSDMenu
{

    public static function menu(USSDRequest $request, array $sessionData): array
    {
        if (count($sessionData) === 2) {
            $validatePIN = validatePIN($request->getMSISDN(), last($sessionData));
            if ($validatePIN->fails()) {
                clearSessionData($request->getSessionId());
                return endedSessionMessage($validatePIN->messages()->first());
            }
            return endedSessionMessage(ussdMenu([
                "G-Money Transactions",
                "2024-02-09 Transfer GHS 200.00",
                "2024-02-09 Transfer GHS 50.00",
                "2024-02-09 Top Up GHS 130.00",
                "2024-02-09 Withdrawal GHS 200.00",
            ]));
        }

        return continueSessionMessage(ussdMenu([
            "Transactions",
            "Enter your PIN:"
        ]));
    }
}
