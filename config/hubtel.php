<?php


return [
    'sms' => [
        'endpoint' => 'https://sms.hubtel.com/v1/messages/send',
        'clientId' => env('HUBTEL_SMS_CLIENT_ID'),
        'clientSecret' => env('HUBTEL_SMS_CLIENT_SECRET'),
        'senderId' => env('HUBTEL_SMS_SENDER_ID')
    ]
];
