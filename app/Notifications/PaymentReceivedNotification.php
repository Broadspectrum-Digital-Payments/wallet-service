<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\Notifications\Channels\HubtelSMSService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly Transaction $transaction)
    {
        //
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

    public function toSMS(object $notifiable): string
    {
        return "You have received GHS {$this->transaction->getAmountInMajorUnits()} from {$this->transaction->account_name} [{$this->transaction->account_number}]. Your available balance is GHS {$this->transaction->user->getAvailableBalanceInMinorUnits()}. Transaction ID: {$this->transaction->stan}. Fee: GHS 0";
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
