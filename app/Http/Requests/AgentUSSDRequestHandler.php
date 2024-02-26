<?php


namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Actions\AgentHome;
use App\Http\Responses\ArkeselUSSDResponse;

final class AgentUSSDRequestHandler
{
    public function handle(ArkeselUSSDRequest $request): JsonResponse
    {
        info("USSD Request:", $request->validated());
        $sessionData = $this->cacheSessionData($request);

        if (!str_contains(strtolower($request->getUserData()), 'timeout')) {
            $response = ($this->isAuthorized($request->getMSISDN())) ?
                AgentHome::menu($request, $sessionData) :
                $this->isNotAgentMessage();

            return ArkeselUSSDResponse::message($request, $response);
        }

        return successfulResponse([], status: 204);
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

    public function isAuthorized(string $phoneNumber): bool
    {
        $user = User::findByPhoneNumber($phoneNumber);

        return $user && $user?->type == User::AGENT;
    }

    private function isNotAgentMessage(): array
    {
        return endedSessionMessage(ussdMenu([
            "Welcome to G-Money", "",
            "You are not an authroised agent, Contact G-Money to get started!", "",
            "Thanks"
        ]));
    }
}
