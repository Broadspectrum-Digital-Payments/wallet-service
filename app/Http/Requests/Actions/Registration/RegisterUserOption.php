<?php

namespace App\Http\Requests\Actions\Registration;

use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use App\Models\User;

class RegisterUserOption implements USSDMenu
{

    public static function menu(USSDRequest $request, array $sessionData): array
    {
        $user = [
            'external_id' => uuid_create(),
            'phone_number' => $request->getMSISDN(),
            'name' => $sessionData[4],
            'email' => $sessionData[5],
            'pin' => $sessionData[6]
        ];

//        $walletCreated = ($response = PaytabsWalletService::registerUser($sessionData[3], $user['name'], $user['email'], $user['phone_number'], $user['pin'])) && ($response['status'] ?? null);
        $userCreated = User::query()->create($user);

        if ($userCreated) {
            return endedSessionMessage("GMoney Registration\nCongratulations! You have successfully registered on GMoney. Please dial " . config('ussd.code') . ' to access your wallet.');
        }

        clearSessionData($request->getSessionId());
        return endedSessionMessage("GMoney Registration\nOops! An error occurred, please wait a while and try again.");
    }
}
