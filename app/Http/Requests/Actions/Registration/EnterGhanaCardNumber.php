<?php

namespace App\Http\Requests\Actions\Registration;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class EnterGhanaCardNumber implements USSDMenu
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function menu(USSDRequest $request, array $sessionData): array
    {
        if (getCachedOTP($request->getMSISDN()) <> trim($sessionData[2])) {
            return endedSessionMessage(\ussdMenu(['Error', 'The OTP is wrong please check and try again.']));
        }

        return continueSessionMessage("Enter Ghana card number");
    }
}
