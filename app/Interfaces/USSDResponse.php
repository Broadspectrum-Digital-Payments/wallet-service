<?php

namespace App\Interfaces;

use Illuminate\Http\JsonResponse;

interface USSDResponse
{
    public static function message(USSDRequest $request, array $response): JsonResponse;
}
