<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Actions\Admin\DashboardAggregatorAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardAggregatorAction $action)
    {
        return $action->handle($request);
    }
}
