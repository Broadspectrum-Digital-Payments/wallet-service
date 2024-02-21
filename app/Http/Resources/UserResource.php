<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'ghanaCardNumber' => $this->ghana_card_number,
            'phoneNumber' => $this->phone_number,
            'type' => $this->type,
            'status' => $this->status,
            'kycStatus' => $this->kyc_status,
            'actualBalance' => number_format($this->actual_balance / 100, 2),
            'availableBalance' => number_format($this->available_balance / 100, 2),
            'bearerToken' => $this->when($this->bearerToken, fn() => $this->bearerToken),
            'files' => FileResource::collection($this->whenLoaded('files'))
        ];
    }
}
