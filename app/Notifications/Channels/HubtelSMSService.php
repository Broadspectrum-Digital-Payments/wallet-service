<?php

namespace App\Notifications\Channels;

use Illuminate\Support\Facades\Http;

class HubtelSMSService
{
    public static function send(object $notifiable, $notification)
    {
        $response = Http::withBasicAuth(config('hubtel.sms.clientId'), config('hubtel.sms.clientSecret'))
            ->post(config('hubtel.sms.endpoint'), [
                'From' => config('hubtel.sms.senderId'),
                'To' => $notifiable->phone_number,
                'Content' => $notification->toSMS($notifiable)
            ]);

        info('Send SMS Response', [$response->json()]);

        if ($response->successful()) return $response->json();

        return [];
    }
}
