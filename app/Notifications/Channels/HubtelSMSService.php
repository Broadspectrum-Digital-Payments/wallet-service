<?php

namespace App\Notifications\Channels;

use Illuminate\Support\Facades\Http;

class HubtelSMSService
{
    public static function send(object $notifiable, $notification)
    {
        $response = Http::withBasicAuth("gjycilyy", "dhtthlyz")
            ->post("https://sms.hubtel.com/v1/messages/send", [
                'From' => 'BDP',
                'To' => $notifiable->phone_number,
                'Content' => $notification->toSMS($notifiable)
            ]);

        info('Send SMS Response', [$response->json()]);

        if ($response->successful()) return $response->json();

        return [];
    }
}
