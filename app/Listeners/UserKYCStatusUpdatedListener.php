<?php

namespace App\Listeners;

use App\Events\UserKYCStatusUpdatedEvent;
use App\Notifications\UserKYCStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserKYCStatusUpdatedListener
{

    /**
     * Handle the event.
     */
    public function handle(UserKYCStatusUpdatedEvent $event): void
    {
        $event->user->notify(new UserKYCStatusUpdatedNotification);
    }
}
