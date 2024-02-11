<?php

namespace App\Http\Requests\Actions\Transfer;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;

class TransferMoneyOption implements USSDMenu
{

    public static function menu(USSDRequest $request, array $sessionData): array
    {
        return continueSessionMessage(ussdMenu([
            "Transfer Money",
            "1. G-Money",
            "2. MTN",
            "3. Vodafone",
            "4. AT Money",
            "5. Bank"
        ]));
    }
}
