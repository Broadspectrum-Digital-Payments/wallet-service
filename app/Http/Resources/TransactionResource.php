<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'externalId' => $this->external_id,
            'stan' => $this->stan,
            'processorReference' => $this->processor_reference,
            'type' => $this->type,
            'currency' => 'GHS',
            'accountNumber' => $this->account_number,
            'accountIssuer' => $this->account_issuer,
            'accountName' => $this->account_name,
            'amount' => abs($this->amount),
            'balanceBefore' => $this->balance_before,
            'balanceAfter' => $this->balance_after,
            'fee' => 0,
            'feeInMajorUnits' => '0.00',
            'amountInMajorUnits' => $this->getAmountInMajorUnits(),
            'balanceBeforeInMajorUnits' => $this->getBalanceBeforeInMajorUnits(),
            'balanceAfterInMajorUnits' => $this->getBalanceAfterInMajorUnits(),
            'description' => $this->description,
            'status' => $this->status,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at->format('Y-m-d H:i:s'),
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
