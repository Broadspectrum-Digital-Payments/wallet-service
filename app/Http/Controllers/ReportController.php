<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\Action\TransactionReportAction;

class ReportController extends Controller
{
    public function transactionReport(Request $request, TransactionReportAction $action)
    {
        return $action->handle($request);
    }
}
