<?php

namespace App\Notifications;

use App\Notifications\Channels\HubtelSMSService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OTPNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly string $otp)
    {
        info('Sending OTP');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [HubtelSMSService::class];
    }

    public function toSMS($notifiable): string
    {
        return "Your BSL wallet verification code is: " . $this->otp;
    }
}
