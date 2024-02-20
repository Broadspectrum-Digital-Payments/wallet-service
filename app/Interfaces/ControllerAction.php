<?php

namespace App\Interfaces;

use Illuminate\Http\JsonResponse;

interface ControllerAction
{
    public function handle(HttpRequest $request): JsonResponse;
}
