<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $external_id
 * @property User $user
 * @property string $description
 * @property string $type
 * @property int $amount
 * @property int $balance_before
 * @property int $balance_after
 */
class Transaction extends Model
{
    use HasFactory;

    const COMPLETED = 'completed';
    const FAILED = 'failed';
    const QUEUED = 'queued';
    const REMITTANCE = 'remittance';
    protected $fillable = [
        'stan',
        'amount',
        'external_id',
        'description',
        'type',
        'balance_before',
        'balance_after',
        'account_number',
        'account_name',
        'account_issuer',
        'status',
        'fee',
        'tax'
    ];

    const REMITTANCE_ACCOUNT_ISSUERS = [self::MTN, self::VODAFONE_CASH, self::ATM];
    const P2P_ACCOUNT_ISSUERS = [self::GMO];

    const DEBIT_TYPES = [self::CASH_OUT, self::TRANSFER, self::PAYMENT, self::REVERSAL, self::RESERVED];
    const CREDIT_TYPES = [self::CASH_IN, self::REMITTANCE];
    const CASH_OUT = 'cash out';
    const TRANSFER = 'transfer';
    const PAYMENT = 'payment';
    const REVERSAL = 'reversal';
    const RESERVED = 'reserved';
    const CASH_IN = 'cash in';
    const MTN = 'mtn';
    const VODAFONE_CASH = 'vodafone cash';
    const ATM = 'atm';
    const GMO = 'gmo';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCredit(): bool
    {
        return in_array($this->type, self::CREDIT_TYPES);
    }

    public function isDebit(): bool
    {
        return in_array($this->type, self::DEBIT_TYPES);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::FAILED;
    }

    public function getAmountInMajorUnits(): string
    {
        return number_format(abs($this->amount) / 100, 2);
    }

    public function getBalanceBeforeInMajorUnits(): string
    {
        return number_format($this->balance_before / 100, 2);
    }

    public function getBalanceAfterInMajorUnits(): string
    {
        return number_format($this->balance_after / 100, 2);
    }

    public function isTransfer(): bool
    {
        return $this->type === self::TRANSFER;
    }

    public function isP2P(): bool
    {
        return $this->account_issuer === self::GMO;
    }

    public static function findByExternalId(string $externalID): Model|Builder|null
    {
        return self::query()->where('external_id', '=', $externalID)->first();
    }

    public static function findByStan(string $stan): Model|Builder|null
    {
        return self::query()->where('stan', '=', $stan)->first();
    }

    public function queued(): bool
    {
        return $this->status === Transaction::QUEUED;
    }

    public function isRemittance(): bool
    {
        return $this->type === self::REMITTANCE;
    }
}
