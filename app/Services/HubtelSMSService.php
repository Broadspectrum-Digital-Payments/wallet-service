<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HubtelSMSService
{
    public static function send(string $phoneNumber, string $message)
    {
        $response = Http::withBasicAuth("gjycilyy", "dhtthlyz")
            ->post("https://sms.hubtel.com/v1/messages/send", [
                'From' => 'BDP',
                'To' => $phoneNumber,
                'Content' => $message
            ]);

        info('Send SMS Response', [$response->json()]);

        if ($response->successful()) return $response->json();

        return [];
    }
}
