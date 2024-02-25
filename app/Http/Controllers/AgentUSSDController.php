<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\ArkeselUSSDRequest;
use App\Http\Requests\AgentUSSDRequestHandler;

class AgentUSSDController extends Controller
{
    public function __invoke(AgentUSSDRequestHandler $handler, ArkeselUSSDRequest $request): JsonResponse
    {
        return $handler->handle($request);
    }
}
