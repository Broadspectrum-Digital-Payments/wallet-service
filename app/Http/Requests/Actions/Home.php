<?php

namespace App\Http\Requests\Actions;

use App\Http\Requests\Actions\Transactions\TransactionsOption;
use App\Http\Requests\Actions\Transfer\TransferMoneyOption;
use App\Http\Requests\Actions\Wallet\WalletOption;
use App\Http\Requests\Actions\Withdraw\WithdrawOption;
use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;

class Home implements USSDMenu
{

    public static function menu(USSDRequest $request, array $sessionData): array
    {
        return match ($sessionData[0] ?? null) {
            "1" => TransferMoneyOption::menu($request, $sessionData),
            "2" => WithdrawOption::menu($request, $sessionData),
            "3" => TransactionsOption::menu($request, $sessionData),
            "4" => WalletOption::menu($request, $sessionData),
            null => self::mainMenu(),
            default => unknownOptionMessage()
        };
    }

    private static function mainMenu(): array
    {
        return [
            "continueSession" => true,
            "message" => self::getMenu()
        ];
    }

    private static function getMenu(): string
    {
        return ussdMenu([
            "Welcome to G-Money",
            "1. Transfer Money",
            "2. Withdraw",
            "3. Transactions",
            "4. Wallet"
        ]);
    }
}
