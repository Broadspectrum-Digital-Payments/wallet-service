<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use LaravelIdea\Helper\App\Models\_IH_User_QB;

/**
 * @property int $available_balance
 * @property int $actual_balance
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'name',
        'email',
        'phone_number',
        'ghana_card_number',
        'pin',
        'status',
        'kyc_status',
        'available_balance',
        'actual_balance',
        'type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'pin' => 'hashed'
    ];

    public function login(): void
    {
        $this->tokens()->delete();
        $token = $this->createToken('login');
        $this->bearerToken = $token->plainTextToken;
    }

    public function setPinAttribute(string $pin): void
    {
        $this->attributes['pin'] = bcrypt(trim($pin));
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

    /**
     * Finds a user by their phone number.
     *
     * @param string $phoneNumber The phone number to search for.
     * @return Model|_IH_User_QB|Builder|User|null The user instance matching the phone number, or null if not found.
     */
    public static function findByPhoneNumber(string $phoneNumber): Model|_IH_User_QB|Builder|User|null
    {
        return self::query()->where('phone_number', '=', $phoneNumber)->first();
    }


    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function updateBalances(): void
    {
        $this->actual_balance = $this->transactions()->whereNotIn('status', ['failed'])->sum('amount') ?? 0;
        $this->available_balance = $this->transactions()->where('status', '=', 'completed')->sum('amount') ?? 0;
        $this->saveQuietly();
    }

    public function getAvailableBalanceInMinorUnits(): string
    {
        return number_format($this->available_balance / 100, 2);
    }

    public function transfer(int $amount, string $accountNumber, string $description, string $stan, bool $p2p = true)
    {
        if ($p2p && $user = self::findByPhoneNumber($accountNumber)) {
            return $user->transactions()->create([
                'stan' => $stan,
                'amount' => $amount,
                'description' => $description,
                'external_id' => uuid_create(),
                'account_number' => $this->phone_number,
                'account_issuer' => 'gmo',
                'account_name' => $this->name,
                'type' => Transaction::CASH_IN,
                'status' => Transaction::COMPLETED
            ]);
        }

        return null;
    }
}
