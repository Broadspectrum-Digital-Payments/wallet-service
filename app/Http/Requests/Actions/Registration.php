<?php

namespace App\Http\Requests\Actions;

use App\Http\Requests\Actions\Registration\ConfirmPINOption;
use App\Http\Requests\Actions\Registration\CreatePINOption;
use App\Http\Requests\Actions\Registration\EnterEmailOption;
use App\Http\Requests\Actions\Registration\EnterFullNameOption;
use App\Http\Requests\Actions\Registration\EnterOTPOption;
use App\Http\Requests\Actions\Registration\GenerateOTP;
use App\Http\Requests\Actions\Registration\InitiateRegistration;
use App\Http\Requests\Actions\Registration\RegisterUserOption;
use App\Http\Requests\Actions\Registration\VerifyOTP;
use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;

class Registration implements USSDMenu
{

    /**
     */
    public static function menu(USSDRequest $request, array $sessionData): array
    {
        $registrationStep = count($sessionData);

        if ($registrationStep === 0) return InitiateRegistration::menu($request, $sessionData);
        if ($registrationStep === 1) return GenerateOTP::menu($request, $sessionData);
        if ($registrationStep === 2) return EnterOTPOption::menu($request, $sessionData);
        if ($registrationStep === 3) return VerifyOTP::menu($request, $sessionData);
        if ($registrationStep === 4) return EnterFullNameOption::menu($request, $sessionData);
        if ($registrationStep === 5) return EnterEmailOption::menu($request, $sessionData);
        if ($registrationStep === 6) return CreatePINOption::menu($request, $sessionData);
        if ($registrationStep === 7) return ConfirmPINOption::menu($request, $sessionData);
        if ($registrationStep === 8) return RegisterUserOption::menu($request, $sessionData);

        return unknownOptionMessage();
    }
}
