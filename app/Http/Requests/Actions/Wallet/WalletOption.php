<?php

namespace App\Http\Requests\Actions\Wallet;

use App\Http\Requests\ArkeselUSSDRequest;
use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use App\Services\WalletService;

/**
 * Class WalletOption
 *
 * The WalletOption class implements the USSDMenu interface to handle menu options for a USSD request.
 */
class WalletOption implements USSDMenu
{

    /**
     * Handle menu options for a USSD request.
     *
     * @param USSDRequest $request The USSD request object.
     * @param array $sessionData The session data associated with the request.
     *
     * @return array The response message as an array.
     */
    public static function menu(USSDRequest $request, array $sessionData): array
    {
        if (count($sessionData) > 1) {
            return match ($sessionData[1]) {
                "1" => self::handleCheckBalance($request, $sessionData),
                "2" => self::handleChangPIN($request, $sessionData),
                default => unknownOptionMessage()
            };
        }

        return continueSessionMessage(ussdMenu([
            "Wallet",
            "1. Check balance",
            "2. Change PIN",
            "3. Approvals"
        ]));
    }

    /**
     * Handles the check balance functionality.
     *
     * @param ArkeselUSSDRequest $request The USSD request object.
     * @param mixed $sessionData The session data.
     * @return array The response message to be sent back to the user.
     */
    private static function handleCheckBalance(ArkeselUSSDRequest $request, array $sessionData): array
    {
        if (self::isSecondLevelOption($sessionData)) return continueSessionMessage(ussdMenu([
            "Check balance",
            "Enter PIN:"
        ]));

        $pinValidation = validatePIN($request->getMSISDN(), last($sessionData));
        $message = ($pinValidation->fails()) ? $pinValidation->messages()->first() : ussdMenu([
            "Check balance",
            "Your account balance is GHS 56.32"
        ]);

        clearSessionData($request->getSessionId());
        return endedSessionMessage($message);
    }

    /**
     * @param $sessionData
     * @return bool
     */
    public static function isSecondLevelOption($sessionData): bool
    {
        return count($sessionData) === 2;
    }

    /**
     * Handles the process for changing PIN in the USSD application.
     *
     * @param ArkeselUSSDRequest $request The USSD request object.
     * @param array $sessionData The session data array.
     * @return array The response message as an array.
     */
    private static function handleChangPIN(ArkeselUSSDRequest $request, array $sessionData): array
    {
        if (self::isSecondLevelOption($sessionData)) return continueSessionMessage(ussdMenu([
            "Change PIN",
            "Enter current PIN:"
        ]));

        if (self::isThirdLevelOption($sessionData)) return continueSessionMessage(ussdMenu([
            "Change PIN",
            "Enter new PIN:"
        ]));

        if (self::isForthLevelOption($sessionData)) return continueSessionMessage(ussdMenu([
            "Change PIN",
            "Confirm new PIN:"
        ]));

        if (self::isFifthLevelOption($sessionData)) {
            // Send change PIN request, this triggers an OTP
            if (($response = WalletService::changePIN($sessionData[3], $sessionData[4])) && $response['status'] ?? null) {
                sleep(2);
                return continueSessionMessage(ussdMenu([
                    "Change PIN",
                    "Enter the OTP sent to your number:"
                ]));
            }

            return operationFailedMessage();
        }

        if (self::isSixthLevelOption($sessionData)) {
            // Send PIN change confirmation
            $message = (($response = WalletService::confirmChangePIN($sessionData[3], $sessionData[4], $sessionData[5])) && $response['status'] ?? null) ?
                "You have successfully changed your PIN" :
                "PIN change failed, please try again later";

            return endedSessionMessage(\ussdMenu([
                $message
            ]));
        }

        return unknownOptionMessage();
    }

    /**
     * @param $sessionData
     * @return bool
     */
    public static function isThirdLevelOption($sessionData): bool
    {
        return count($sessionData) === 3;
    }

    private static function isForthLevelOption(array $sessionData): bool
    {
        return count($sessionData) === 4;
    }

    /**
     * Checks if the given session data has exactly 5 elements.
     *
     * @param array $sessionData The session data to check.
     * @return bool Returns true if the session data has exactly 5 elements, false otherwise.
     */
    private static function isFifthLevelOption(array $sessionData): bool
    {
        return count($sessionData) === 5;
    }

    /**
     * Check if the given $sessionData has six elements.
     *
     * @param array $sessionData The session data to be checked.
     * @return bool Returns true if the session data has exactly six elements, false otherwise.
     */
    private static function isSixthLevelOption(array $sessionData): bool
    {
        return count($sessionData) === 6;
    }
}
