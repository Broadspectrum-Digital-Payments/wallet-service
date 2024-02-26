<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

/**
 * Class MiddlewareService
 *
 * Provides methods for collecting payments from mobile money accounts.
 */
class MiddlewareService
{
    /**
     * Collects a payment from a mobile money account.
     *
     * @param string $accountNumber The account number to collect the payment from.
     * @param string $accountIssuer The issuer of the account (e.g. MTN, Airtel, Vodafone).
     * @param string $reference The reference number for the payment.
     * @param int $amount The amount to collect.
     * @param string $description The description of the payment.
     * @return array The response from the collection API if the collection was successful, otherwise an empty array.
     */
    public static function collect(string $accountNumber, string $accountIssuer, string $reference, int $amount, string $description): array
    {
        try {
            $response = Http::withToken(config('services.middleware.bearerToken'))
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post(
                    url: config('services.middleware.baseUrl') . '/api/v1.0/mobileMoney/collections',
                    data: [
                        'accountNumber' => $accountNumber,
                        'rSwitch' => $accountIssuer,
                        'amount' => $amount,
                        'callbackUrl' => route('middleware.callback'),
                        'description' => $description,
                        'reference' => $reference
                    ]);

            info('Middleware Collection Response:', $response->json());

            if ($response->successful()) return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return [];
    }
}
