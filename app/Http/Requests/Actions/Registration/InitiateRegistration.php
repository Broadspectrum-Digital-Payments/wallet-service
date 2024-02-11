<?php

namespace App\Http\Requests\Actions\Registration;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;

class InitiateRegistration implements USSDMenu
{

    public static function menu(USSDRequest $request, array $sessionData): array
    {
        return continueSessionMessage("Welcome to GMoney\n1. Initiate registration");
    }
}
