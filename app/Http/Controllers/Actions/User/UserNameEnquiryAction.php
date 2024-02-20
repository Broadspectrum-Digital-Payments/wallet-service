<?php

namespace App\Http\Controllers\Actions\User;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserNameEnquiryAction
{
    public function handle(Request $request)
    {
        try {
            if ($user = User::query()->where('phone_number', '=', $request->query('phoneNumber'))->first()) {
                return successfulResponse([
                    'data' => [
                        'name' => $user->name,
                        'phoneNumber' => $user->phone_number,
                        'status' => $user->status
                    ]
                ], "Wallet found");
            }

            return errorResponse("Wallet does not exist", ResponseAlias::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            report($exception);
        }

        return errorResponse("Something went wrong, please again later.");
    }
}
