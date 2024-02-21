<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Actions\User\UserRegistrationAction;
use App\Http\Requests\UserRegistrationRequest;
use Illuminate\Http\JsonResponse;

/**
 * Class AgentController
 *
 * This class is responsible for handling user registration.
 *
 * @package App\Controllers
 */
class AgentController extends Controller
{
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
