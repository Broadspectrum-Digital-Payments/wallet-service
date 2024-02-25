<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;

class TransactionsExport implements FromQuery, Responsable, WithHeadings
{
    use Exportable;

    public function __construct(protected Builder $transaction)
    {
    }

    public function headings(): array
    {
        return [];
    }

    // externalId, startend, enddate, status

    public function query()
    {
        return $this->transaction->latest();
    }
}
