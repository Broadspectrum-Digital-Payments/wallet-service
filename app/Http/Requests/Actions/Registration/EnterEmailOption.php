<?php

namespace App\Http\Requests\Actions\Registration;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;

class EnterEmailOption implements USSDMenu
{

    public static function menu(USSDRequest $request, array $sessionData): array
    {
        return continueSessionMessage("G Money Registration\nEnter your email");
    }
}
