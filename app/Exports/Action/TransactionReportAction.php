<?php

declare(strict_types=1);

namespace App\Exports\Action;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;

final class TransactionReportAction
{
    public function handle(Request $request)
    {
        try {
            $transactions = Transaction::query()
                ->when($externalId = $request->query('externalId'), fn (Builder $query) => $query->where('external_id', '=', $externalId))
                ->when($startDate = $request->query('startDate'), fn (Builder $query) => $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay()->toDateTimeString()))
                ->when($endDate = $request->query('endDate'), fn (Builder $query) => $query->where('created_at', '<=', Carbon::parse($endDate)->startOfDay()->toDateTimeString()))
                ->when($status = $request->query('status'), fn (Builder $query) => $query->where('status', '=', $status))
                ->latest()
                ->take($request->query('rowNum', 500));

            return Excel::download(
                new TransactionsExport($transactions),
                'transactions.csv',
                \Maatwebsite\Excel\Excel::CSV,
                [
                    'Content-Type' => 'text/csv',
                ]
            );
        } catch (\Exception $exception) {
            report($exception);
        }

        return errorResponse("Requested data not found.", Response::HTTP_NOT_FOUND);
    }
}
