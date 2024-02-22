<?php

namespace App\Http\Requests\Actions\Registration;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use App\Models\User;
use App\Notifications\UserRegisteredNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class RegisterUserOption
 *
 * This class handles the registration of a new user in the GMoney system.
 *
 * Implements USSDMenu interface to define the menu functionality.
 */
class RegisterUserOption implements USSDMenu
{

    /**
     * Process the menu request and register a new user in the GMoney system.
     *
     * @param USSDRequest $request The USSD request object.
     * @param array $sessionData The session data array containing user information.
     *                           The array should have the following keys:
     *                           - 0: Session ID
     *                           - 1: Service Code
     *                           - 2: User Input
     *                           - 3: Ghana Card Number
     *                           - 4: PIN
     *
     * @return array The USSD response message as an array.
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function menu(USSDRequest $request, array $sessionData): array
    {
        if (getCachedOTP($request->getMSISDN()) <> trim($sessionData[2])) {
            return endedSessionMessage(\ussdMenu(['Error', 'The OTP is wrong please check and try again.']));
        }

        $pinValidation = Validator::make([
            'pin' => $sessionData[5],
            'pin_confirmation' => $sessionData[5]
        ], [
            'pin' => ['required', 'digits:6'],
            'pin_confirmation' => ['required', 'same:pin']
        ]);

        if ($pinValidation->fails()) {
            return endedSessionMessage(\ussdMenu([$pinValidation->messages()->first()]));
        }

        $user = User::query()->create([
            'external_id' => uuid_create(),
            'phone_number' => $request->getMSISDN(),
            'ghana_card_number' => $sessionData[3],
            'name' => $sessionData[4],
            'pin' => $sessionData[5],
            'type' => 'user'
        ]);

        clearSessionData($request->getSessionId());

        if ($user) {
            Notification::send($user, new UserRegisteredNotification);
            return endedSessionMessage("G - Money Registration\nAccount created successfully. Please dial " . config('ussd.code') . ' to access your wallet.');
        }

        return endedSessionMessage("GMoney Registration\nOops! An error occurred, please wait a while and try again.");
    }
}
