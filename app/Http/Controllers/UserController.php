<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Actions\User\UserKYCAction;
use App\Http\Controllers\Actions\User\UserLoginAction;
use App\Http\Controllers\Actions\User\UserNameEnquiryAction;
use App\Http\Controllers\Actions\User\UserOTPAction;
use App\Http\Controllers\Actions\User\UserOTPVerificationAction;
use App\Http\Controllers\Actions\User\UserRegistrationAction;
use App\Http\Requests\SendUserOTPRequest;
use App\Http\Requests\UploadFileRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\VerifyUserOTPRequest;
use App\Http\Resources\FileResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(UserLoginRequest $request, UserLoginAction $action)
    {
        return $action->handle($request);
    }

    public function register(UserRegistrationRequest $request, UserRegistrationAction $action)
    {
        return $action->handle($request);
    }

    public function me(Request $request)
    {
        return successfulResponse(['data' => new UserResource($request->user())]);
    }

    public function nameEnquiry(Request $request, UserNameEnquiryAction $action)
    {
        return $action->handle($request);
    }

    public function kyc(UploadFileRequest $request, UserKYCAction $action)
    {
        return $action->handle($request);
    }

    public function docs(Request $request)
    {
        return FileResource::collection($request->user()->files()->get());
    }

    public function sendOTP(SendUserOTPRequest $request, UserOTPAction $action)
    {
        return $action->handle($request);
    }

    public function verifyOTP(VerifyUserOTPRequest $request, UserOTPVerificationAction $action)
    {
        return $action->handle($request);
    }
}
