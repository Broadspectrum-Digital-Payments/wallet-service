<?php

namespace App\Http\Controllers\Actions\User;

use App\Http\Requests\UploadFileRequest;
use App\Http\Resources\FileResource;
use App\Interfaces\ControllerAction;
use App\Interfaces\HttpRequest;
use App\Models\File;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserKYCAction implements ControllerAction
{

    public function handle(UploadFileRequest|HttpRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $uploadedFiles = [];

            $selfie = $request->file('selfie');
            $ghanaCardFront = $request->file('ghana-card-front');
            $ghanaCardBack = $request->file('ghana-card-back');

            if ($selfie) $uploadedFiles[] = File::upload($selfie, 'selfie', $user);
            if ($ghanaCardFront) $uploadedFiles[] = File::upload($ghanaCardFront, 'ghana-card-front', $user);
            if ($ghanaCardBack) $uploadedFiles[] = File::upload($ghanaCardBack, 'ghana-card-back', $user);

            if (!empty($uploadedFiles)) {
                if ($user->files()->count() === 3) {
                    $user->update(['kyc_status' => 'submitted']);
                } else {
                    $user->update(['kyc_status' => 'started']);
                }
                return successfulResponse([
                    'data' => FileResource::collection($uploadedFiles)
                ], 'File uploaded.', ResponseAlias::HTTP_CREATED);
            }


            return errorResponse("No file uploaded, please try again.", ResponseAlias::HTTP_BAD_REQUEST);

        } catch (Exception $exception) {
            report($exception);
        }

        return errorResponse();
    }
}
