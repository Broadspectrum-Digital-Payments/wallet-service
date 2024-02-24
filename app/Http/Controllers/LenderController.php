<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Controllers\Actions\Lender\LenderLoginAction;
use App\Http\Controllers\Actions\User\UserRegistrationAction;

/**
 * Class AgentController
 *
 * This class is responsible for handling user registration.
 *
 * @package App\Controllers
 */
class LenderController extends Controller
{
    /**
     * Login a user.
     *
     * @param UserLoginRequest $request The user login request object containing the user details.
     * @param LenderLoginAction $action The action object used to handle the user login.
     * @return JsonResponse The result of the user login action handling.
     */
    public function login(UserLoginRequest $request, LenderLoginAction $action)
    {
        return $action->handle($request);
    }

    /**
     * Registers a user.
     *
     * @param UserRegistrationRequest $request The user registration request object containing the user details.
     * @param UserRegistrationAction $action The action object used to handle the user registration.
     * @return JsonResponse The result of the user registration action handling.
     */
    public function register(UserRegistrationRequest $request, UserRegistrationAction $action)
    {
        return $action->handle($request);
    }
}
