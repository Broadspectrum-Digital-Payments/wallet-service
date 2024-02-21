<?php


use App\Rules\PINMatchRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

function continueSessionMessage(string $message): array
{
    return [
        "continueSession" => true,
        "message" => $message
    ];
}

function endedSessionMessage(string $message): array
{
    return [
        "continueSession" => false,
        "message" => $message
    ];
}

function unknownOptionMessage(): array
{
    return endedSessionMessage("Unknown input, please try again.");
}

function operationFailedMessage(): array
{
    return endedSessionMessage("Operation failed, please try again later.");
}

function updateSessionData(string $sessionId, mixed $data = null): array
{
    $sessionData = cache($sessionId) ?? [];
    if ($data) $sessionData[] = $data;
    cache([$sessionId => $sessionData], now()->addSeconds(30));
    return $sessionData;
}

function clearSessionData(string $sessionId): void
{
    cache([$sessionId => null]);
}

function ussdMenu(array $menuItems): string
{
    return implode("\n", $menuItems);
}

function validatePIN(string $phoneNumber, string $pin)
{
    return Validator::make([
        'pin' => $pin
    ], [
        'pin' => ['required', 'digits:6', new PINMatchRule($phoneNumber)]
    ], [
        'pin.digits' => 'PIN must be 6 digits'
    ]);
}

function errorResponse(string $message = "Something went wrong, please try again later.", int $status = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
{
    return \jsonResponse(
        [
            'success' => false,
            'message' => $message
        ], $status
    );
}

function successfulResponse(array $data, string $message = "Operation successful", int $status = Response::HTTP_OK): JsonResponse
{
    return \jsonResponse(
        [
            'success' => true,
            ...$data,
            'message' => $message
        ], $status
    );
}

function jsonResponse(array $data = [], int $status = Response::HTTP_OK): JsonResponse
{
    return response()->json($data, $status);
}

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function getCachedOTP(string $phoneNumber)
{
    return cache()->get($phoneNumber . 'otp');
}

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function checkOTP(string $phoneNumber, string $otp): bool
{
    return ($cachedOTP = getCachedOTP($phoneNumber)) && $cachedOTP == $otp;
}

function getPaginatedData(LengthAwarePaginator $paginator, int $pageSize): array
{
    return [
        'previousPage' => $paginator->previousPageUrl(),
        'nextPage' => $paginator->nextPageUrl(),
        'currentPage' => $paginator->currentPage(),
        'onLastPage' => $paginator->onLastPage(),
        'onFirstPage' => $paginator->onFirstPage(),
        'pageSize' => $pageSize
    ];
}

function generateStan(): string
{
    return now()->format('ymdHisu');
}
