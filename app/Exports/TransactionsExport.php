<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TransactionsExport implements
    FromQuery,
    WithHeadings,
    WithColumnFormatting,
    WithMapping,
    Responsable
{
    use Exportable;

    public function __construct(protected mixed $transactions)
    {
    }

    public function headings(): array
    {
        return [
            'STAN',
            'Processor Reference',
            'Type',
            'Currency',
            'Account Number',
            'Account Issuer',
            'Account Name',
            'Amount',
            'Balance Before',
            'Balance After',
            'Fee',
            'FeeInMajorUnits',
            'AmountInMajorUnits',
            'BalanceBeforeInMajorUnits',
            'BalanceAfterInMajorUnits',
            'Description',
            'Status',
            'Date Created',
            'Date Updated',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->stan,
            $transaction->processor_reference,
            $transaction->type,
            'GHS',
            $transaction->account_number,
            $transaction->account_issuer,
            $transaction->account_name,
            $transaction->amount,
            $transaction->balance_before,
            $transaction->balance_after,
            0,
            '0.00',
            $this->getAmountInMajorUnits($transaction->amount),
            $this->getBalanceBeforeInMajorUnits($transaction->balance_after),
            $this->getBalanceAfterInMajorUnits($transaction->balance_after),
            $transaction->description,
            $transaction->status,
            $transaction->created_at->format('Y-m-d H:i:s'),
            $transaction->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function columnFormats(): array
    {
        return [];
    }

    public function query()
    {
        return $this->transactions;
    }

    public function getAmountInMajorUnits($amount): string
    {
        return number_format(abs($amount) / 100, 2);
    }

    public function getBalanceBeforeInMajorUnits($balance_before): string
    {
        return number_format($balance_before / 100, 2);
    }

    public function getBalanceAfterInMajorUnits($balance_after): string
    {
        return number_format($balance_after / 100, 2);
    }
}
