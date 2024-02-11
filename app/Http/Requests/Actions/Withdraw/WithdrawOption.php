<?php

namespace App\Http\Requests\Actions\Withdraw;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;

class WithdrawOption implements USSDMenu
{

    public static function menu(USSDRequest $request, array $sessionData): array
    {
        return continueSessionMessage(ussdMenu([
            "Withdraw",
            "Enter merchant ID"
        ]));
    }
}
