<?php

namespace App\Http\Requests\Actions;

use App\Http\Requests\Actions\Registration\ConfirmPINOption;
use App\Http\Requests\Actions\Registration\CreatePINOption;
use App\Http\Requests\Actions\Registration\EnterFullNameOption;
use App\Http\Requests\Actions\Registration\EnterGhanaCardNumber;
use App\Http\Requests\Actions\Registration\EnterOTPOption;
use App\Http\Requests\Actions\Registration\GenerateOTP;
use App\Http\Requests\Actions\Registration\InitiateRegistration;
use App\Http\Requests\Actions\Registration\RegisterUserOption;
use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Random\RandomException;

class Registration implements USSDMenu
{

    /**
     * Menu method for handling different registration steps.
     *
     * This method accepts a USSDRequest object and an array of session data,
     * and returns an array containing the menu response for the current registration step.
     *
     * @param USSDRequest $request The USSDRequest object containing the user input and other request data.
     * @param array $sessionData The array containing the session data for the current user session.
     *
     * @return array The menu response for the current registration step.
     *               Returns the menu response from the corresponding registration step class if the step is valid.
     *               Returns the unknown option message if the step is unknown.
     * @throws RandomException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public static function menu(USSDRequest $request, array $sessionData): array
    {
        if ($request->getNewSession() && getCachedOTP($request->getMSISDN())) {
            info("Continue registration...");
            clearSessionData($request->getSessionId());
            updateSessionData($request->getSessionId(), '1');
            updateSessionData($request->getSessionId(), 'otp');
        }

        $registrationStep = count($sessionData);

        if ($registrationStep === 0) return InitiateRegistration::menu($request, $sessionData);
        if ($registrationStep === 1) return GenerateOTP::menu($request, $sessionData);
        if ($registrationStep === 2) return EnterOTPOption::menu($request, $sessionData);
        if ($registrationStep === 3) return EnterGhanaCardNumber::menu($request, $sessionData);
        if ($registrationStep === 4) return EnterFullNameOption::menu($request, $sessionData);
        if ($registrationStep === 5) return CreatePINOption::menu($request, $sessionData);
        if ($registrationStep === 6) return ConfirmPINOption::menu($request, $sessionData);
        if ($registrationStep === 7) return RegisterUserOption::menu($request, $sessionData);

        return unknownOptionMessage();
    }
}
