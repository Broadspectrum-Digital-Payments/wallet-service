<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArkeselUSSDRequest;
use App\Http\Requests\USSDRequestHandler;
use Illuminate\Http\JsonResponse;

class USSDController extends Controller
{
    public function __invoke(USSDRequestHandler $handler, ArkeselUSSDRequest $request): JsonResponse
    {
        return $handler->handle($request);
    }
}
