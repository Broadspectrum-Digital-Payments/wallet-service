<?php

namespace App\Http\Controllers\Actions\Transaction;

use App\Http\Requests\CreateUserTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Interfaces\ControllerAction;
use App\Interfaces\HttpRequest;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * Class CreateTransactionAction
 */
class CreateTransactionAction implements ControllerAction
{

    /**
     * Handles the given HTTP request or CreateUserTransactionRequest.
     *
     * @param HttpRequest|CreateUserTransactionRequest $request The request to handle.
     * @return JsonResponse The JSON response.
     */
    public function handle(HttpRequest|CreateUserTransactionRequest $request): JsonResponse
    {
        try {
            if ($this->transactingOnSameAccount($request)) {
                return errorResponse("Transactions cannot be performed against same account.", ResponseAlias::HTTP_BAD_REQUEST);
            }

            if ($this->gMoneyAccountDoesNotExist($request)) {
                $accountNumber = $request->validated('account_number');
                return errorResponse("G - Money account number: $accountNumber does not exist", ResponseAlias::HTTP_BAD_REQUEST);
            }

            if ($this->insufficientFunds($request)) {
                return errorResponse("Insufficient funds, please top up and try again.", ResponseAlias::HTTP_BAD_REQUEST);
            }

            $transaction = $this->createTransaction($request);
            return successfulResponse(['data' => new TransactionResource($transaction)], "Transaction queued for processing.", ResponseAlias::HTTP_ACCEPTED);
        } catch (Exception $exception) {
            report($exception);
        }

        return errorResponse();
    }

    private function createTransaction($request) : Transaction
    {
        $transaction = new Transaction([...$request->validated(), 'stan' => generateStan()]);
        $transaction->user()->associate($request->user());
        $transaction->save();

        return $transaction;
    }

    private function transactingOnSameAccount($request) : bool
    {
        return $request->validated('account_number') == $request->user()->phone_number;
    }

    private function gMoneyAccountDoesNotExist($request) : bool
    {
        return $request->validated('account_issuer') == Transaction::GMO && !User::findByPhoneNumber($request->validated('account_number'));
    }

    private function insufficientFunds($request): bool
    {
        return in_array($request->validated('type'), Transaction::DEBIT_TYPES) && $request->user()->available_balance < $request->validated('amount');
    }
}
