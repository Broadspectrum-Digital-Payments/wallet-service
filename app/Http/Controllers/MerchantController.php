<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Actions\User\UserRegistrationAction;
use App\Http\Requests\UserRegistrationRequest;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function register(UserRegistrationRequest $request, UserRegistrationAction $action)
    {
        return $action->handle($request);
    }
}
