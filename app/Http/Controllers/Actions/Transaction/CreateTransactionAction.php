<?php

namespace App\Http\Controllers\Actions\Transaction;

use App\Http\Requests\CreateUserTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * Class CreateTransactionAction
 */
class CreateTransactionAction
{

    /**
     * Handles the given HTTP request or CreateUserTransactionRequest.
     *
     * @param CreateUserTransactionRequest $request The request to handle.
     * @param User $user
     * @return JsonResponse The JSON response.
     */
    public function handle(CreateUserTransactionRequest $request, User $user): JsonResponse
    {
        try {
            if ($this->transactingOnSameAccount($request, $user)) {
                return errorResponse("Transactions cannot be performed against same account.", ResponseAlias::HTTP_BAD_REQUEST);
            }

            if ($this->gMoneyAccountDoesNotExist($request)) {
                $accountNumber = $request->validated('account_number');
                return errorResponse("G - Money account number: $accountNumber does not exist", ResponseAlias::HTTP_BAD_REQUEST);
            }

            if ($this->insufficientFunds($request, $user)) {
                return errorResponse("Insufficient funds, please top up and try again.", ResponseAlias::HTTP_BAD_REQUEST);
            }

            $transaction = $this->createTransaction($request, $user);
            return successfulResponse(['data' => new TransactionResource($transaction)], "Transaction queued for processing.", ResponseAlias::HTTP_ACCEPTED);
        } catch (Exception $exception) {
            report($exception);
        }

        return errorResponse();
    }

    private function createTransaction($request, User $user) : Transaction
    {
        $transaction = new Transaction([...$request->validated(), 'stan' => generateStan()]);
        $transaction->user()->associate($user);
        $transaction->save();

        return $transaction;
    }

    private function transactingOnSameAccount($request, $user) : bool
    {
        return $request->validated('account_number') == $user->phone_number;
    }

    private function gMoneyAccountDoesNotExist($request) : bool
    {
        return $request->validated('account_issuer') == Transaction::GMO && !User::findByPhoneNumber(phoneNumberToInternationalFormat($request->validated('account_number')));
    }

    private function insufficientFunds($request, $user): bool
    {
        return in_array($request->validated('type'), Transaction::DEBIT_TYPES) && $user->available_balance < $request->validated('amount');
    }
}
