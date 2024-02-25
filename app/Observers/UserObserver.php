<?php

namespace App\Observers;

use App\Events\UserKYCStatusUpdatedEvent;
use App\Events\UserStatusUpdatedEvent;
use App\Models\User;
use App\Services\LoanService;

class UserObserver
{
    public function creating(User $user): void
    {
        $user->external_id = uuid_create();
    }
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->type === 'user') LoanService::registerBorrower($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->isDirty('status')) event(new UserStatusUpdatedEvent($user));
        if ($user->isDirty('kyc_status')) event(new UserKYCStatusUpdatedEvent($user));
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
