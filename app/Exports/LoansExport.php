<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class LoansExport implements
    // FromArray,
    FromCollection,
    WithHeadings,
    WithColumnFormatting
{
    public function __construct(protected array $data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'External ID',
            'Stan',
            'Principal',
            'Time',
            'Principal In GHS',
            'Interest',
            'Interest In GHS',
            'Interest Rate',
            'Monthly Installment',
            'Monthly Installment In GHS',
            'Total Repayment Amount',
            'Total Repayment Amount In GHS',
            'Taxes',
            'Taxes In GHS',
            'Fees',
            'Fees In GHS',
            'Status',
            'Created At',
            'Approved At',
            'Disbursed At',
        ];
    }

    public function columnFormats(): array
    {
        return [];
    }

    public function collection()
    {
        $loans = collect($this->data['loans']);
        $stats = collect($this->data['stats']);

        if (!empty($loans)) {
            $statsRows = $stats->map(function ($value, $key) {
                return [$key, $value];
            })->toArray();

            $emptyRow = ['', ''];
            $loans = $loans->concat([$emptyRow])->concat($statsRows);
        }

        return $loans;
    }

    public function array(): array
    {
        $loans = $this->data['loans'];
        $stats = $this->data['stats'];

        $summaryRow = [
            'Total Paid',
            'Total Submitted',
            'Total Collected',
        ];

        foreach ($stats as $key => $value) {
            $summaryRow[] = $value;
        }

        $exportData = array_merge($loans, [$summaryRow]);

        return $exportData;
    }
}
