<?php

namespace App\Http\Controllers\Actions\Admin;

use Exception;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\LoanService;
use Illuminate\Support\Facades\DB;

class DashboardAggregatorAction
{
    public function handle(Request $request)
    {
        try {
            $transactionStats = $this->getTransactionsAggregate();

            $loanStats = collect($this->getLoansAggregate()['data']['loans'])->groupBy(function ($item) {
                return date('M', strtotime($item['createdAt']));
            })->map(function ($groupedData) {
                $loanVolume = $groupedData->count();
                $loanValue = $groupedData->sum('principal');
                return compact('loanVolume', 'loanValue');
            });

            $aggregate = collect($transactionStats)->map(function ($item) use ($loanStats) {
                $loan = $loanStats->get($item['name'], ['loanVolume' => 0, 'loanValue' => 0]);
                return array_merge($item, $loan);
            })->values()->all();

            return successfulResponse(data: array_values($aggregate));
        } catch (Exception $exception) {
            report($exception);
        }

        return errorResponse();
    }


    private function getTransactionsAggregate()
    {
        return Transaction::select(
            DB::raw('DATE_FORMAT(created_at, "%b") as name'),
            DB::raw('COUNT(*) as transactionVolume'),
            DB::raw('SUM(amount) as transactionValue')
        )
            ->where('created_at', '>=', now()->subYear())
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'), 'name')
            ->orderBy(DB::raw('YEAR(created_at)'), 'desc')
            ->orderBy(DB::raw('MONTH(created_at)'), 'desc')
            ->limit(6)
            ->get()
            ->toArray();
    }
    private function getLoansAggregate()
    {
        $response = LoanService::makeRequest('/v1/loans');

        return $response->successful() ? $response->json() : [];
    }
}
