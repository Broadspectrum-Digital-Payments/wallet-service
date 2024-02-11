<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Translation\PotentiallyTranslatedString;

class PINMatchRule implements ValidationRule
{
    private User|Model $user;

    public function __construct(private readonly string $phoneNumber)
    {
        $this->user = User::query()->where('phone_number', '=', $this->phoneNumber)->first();
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value <> $this->user->pin) $fail("Wrong PIN, please try again.");
    }
}
