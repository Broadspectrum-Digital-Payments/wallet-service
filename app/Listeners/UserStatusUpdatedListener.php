<?php

namespace App\Listeners;

use App\Events\UserStatusUpdatedEvent;
use App\Notifications\UserStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserStatusUpdatedListener
{

    /**
     * Handle the event.
     */
    public function handle(UserStatusUpdatedEvent $event): void
    {
        $event->user->notify(new UserStatusUpdatedNotification);
    }
}
