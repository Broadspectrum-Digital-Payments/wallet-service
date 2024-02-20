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
            'processorReference' => $this->processor_reference,
            'type' => $this->type,
            'amount' => $this->amount,
            'accountNumber' => $this->account_number,
            'accountIssuer' => $this->account_issuer,
            'balanceBefore' => $this->balance_before,
            'balanceAfter' => $this->balance_after,
            'description' => $this->description,
            'status' => $this->status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
