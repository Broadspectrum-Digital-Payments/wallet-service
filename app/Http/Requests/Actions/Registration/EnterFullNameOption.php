<?php

namespace App\Http\Requests\Actions\Registration;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;

class EnterFullNameOption implements USSDMenu
{

    public static function menu(USSDRequest $request, array $sessionData): array
    {
        return continueSessionMessage("GMoney Registration\nEnter your full name:");
    }
}
