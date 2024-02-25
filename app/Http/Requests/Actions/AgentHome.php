<?php

namespace App\Http\Requests\Actions;

use Random\RandomException;
use App\Interfaces\USSDMenu;
use App\Interfaces\USSDRequest;
use Psr\SimpleCache\InvalidArgumentException;
use App\Http\Requests\Actions\Wallet\WalletOption;
use App\Http\Requests\Actions\Transactions\TransactionsOption;
use App\Http\Requests\Actions\AgentCashTransaction\CashInOption;
use App\Http\Requests\Actions\AgentCashTransaction\CashOutOption;

class AgentHome implements USSDMenu
{

    /**
     * @throws InvalidArgumentException
     * @throws RandomException
     */
    public static function menu(USSDRequest $request, array $sessionData): array
    {
        return match ($sessionData[0] ?? null) {
            "1" => CashInOption::menu($request, $sessionData),
            "2" => CashOutOption::menu($request, $sessionData),
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
            "1. Cash in",
            "2. Cash out",
            "3. Transactions",
            "4. Wallet"
        ]);
    }
}
