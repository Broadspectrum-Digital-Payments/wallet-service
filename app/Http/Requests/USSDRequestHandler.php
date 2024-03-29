<?php

namespace App\Http\Requests;

use App\Http\Requests\Actions\Home;
use App\Http\Requests\Actions\Registration;
use App\Http\Responses\ArkeselUSSDResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class USSDRequestHandler
{
    /**
     */
    public function handle(ArkeselUSSDRequest $request): JsonResponse
    {
        info("USSD Request:", $request->validated());
        $sessionData = $this->cacheSessionData($request);

        $response = ($this->hasWallet($request->getMSISDN())) ?
            Home::menu($request, $sessionData) :
            Registration::menu($request, $sessionData);

        return ArkeselUSSDResponse::message($request, $response);
    }

    /**
     * @param ArkeselUSSDRequest $request
     * @return array
     */
    public function cacheSessionData(ArkeselUSSDRequest $request): array
    {
        return updateSessionData($request->getSessionId(), $request->getNewSession() ? null : $request->getUserData());
    }

    /**
     * @param string $phoneNumber
     * @return bool
     */
    public function hasWallet(string $phoneNumber): bool
    {
        return User::query()->where('phone_number', '=', $phoneNumber)->count();
    }
}
