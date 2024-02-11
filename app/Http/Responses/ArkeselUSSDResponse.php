<?php

namespace App\Http\Responses;

use App\Interfaces\USSDRequest;
use App\Interfaces\USSDResponse;
use Illuminate\Http\JsonResponse;

class ArkeselUSSDResponse implements USSDResponse
{

    public static function message(USSDRequest $request, array $response): JsonResponse
    {
        $responseData = [
            'sessionID' => $request->getSessionId(),
            'userID' => $request->getUserId(),
            'msisdn' => $request->getMSISDN(),
            ... $response
        ];

        info("USSD Response:", $responseData);

        return response()->json($responseData);
    }
}
