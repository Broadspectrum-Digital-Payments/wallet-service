<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class LoanService
{
    public static function registerBorrower(User $user)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(env('LOAN_BASE_URL'). '/v1/borrowers', [
                'externalId' => $user->external_id,
                'name' => $user->name,
                'phoneNumber' => '0' . substr($user->phone_number, -9)
            ]);
            info("Register borrower response", $response->json());
            return $response->successful() ? $response->json() : [];
        } catch (\Exception $exception) {
            report($exception);
        }

        return [];
    }
}
