<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Random\RandomException;

class WalletService
{

    /**
     * @throws RandomException
     */
    public static function generateOTP(string $phoneNumber): bool
    {
        info("Generating OTP");
        sendOTP($phoneNumber);
        return true;
    }

    /**
     * Execute a POST request to the specified endpoint with the given data.
     *
     * @param string $endpoint The endpoint to send the POST request to.
     * @param array $data The data to include in the POST request.
     *
     * @return Response The response object obtained from the POST request.
     */
    private static function executePostRequest(string $endpoint, array $data, string $token = ""): Response
    {
        $url = config('app.url') . '/v1' . $endpoint;

        info("WalletService Request", ['url' => $url, 'data', $data]);

        $response = Http::withToken($token)->post($url, $data);

        info("WalletService Response", $response->json());

        return $response;
    }

    /**
     * Verify OTP to complete member registration.
     *
     * @param string $phoneNumber The phone number to verify the OTP.
     * @param string $otp The OTP code to verify.
     *
     * @return array The response from the verification process. An empty array is returned if the verification is unsuccessful.
     */
    public static function verifyOTP(string $phoneNumber, string $otp): array
    {
        $response = self::executePostRequest(
            endpoint: '/members/registration/verify-otp',
            data: [
                'medium' => $phoneNumber,
                'otp' => $otp
            ]
        );

        if ($response->successful()) return $response->json();

        return [];
    }

    /**
     * Register a new user.
     *
     * @param string $resourceId The resource ID of the user.
     * @param string $fullName The full name of the user.
     * @param string $email The email address of the user.
     * @param string $phoneNumber The phone number of the user.
     * @param string $pin The PIN of the user.
     *
     * @return array The response from the registration process. An empty array is returned if the registration is unsuccessful.
     */
    public static function registerUser(string $resourceId, string $fullName, string $email, string $phoneNumber, string $pin): array
    {
        $request = self::executePostRequest('/members/registration', [
            'resourceId' => $resourceId,
            'groupId' => 5,
            'name' => $fullName,
            'email' => $email,
            'mobileNo' => $phoneNumber,
            'isMobile' => true,
            'pin' => $pin,
            'confirmPin' => $pin,
        ]);

        if ($request->successful()) return $request->json();

        return [];
    }

    public static function getSessionId(string $phoneNumber, string $PIN): string
    {
        if ($sessionId = cache($phoneNumber . '_sessionId')) {
            return $sessionId;
        }

        $sessionId = self::login(phoneNumber: $phoneNumber, PIN: $PIN);
        cache([$phoneNumber . '_sessionId' => $sessionId]);
        return $sessionId;
    }

    /**
     * Login with phone number and PIN.
     *
     * @param string $phoneNumber The phone number to log in with.
     * @param string $PIN The PIN code for authentication.
     *
     * @return string The session ID if the login is successful and the session is not expired. Otherwise, an empty string is returned.
     */
    private static function login(string $phoneNumber, string $PIN): string
    {
        $response = self::executePostRequest(
            endpoint: "/users/login",
            data: [
                "phoneNumber" => $phoneNumber,
                "pin" => $PIN
            ]
        );

        return ($response->successful() && $response->json('status') && !$response->json('sessionExpired')) ?
            $response->json('sessionId') : "";
    }

    /**
     * Change PIN for the current member.
     *
     * @param string $oldPIN The old PIN to be changed.
     * @param string $newPIN The new PIN to replace the old PIN.
     *
     * @return array The response from the PIN change process. An empty array is returned if the PIN change is unsuccessful.
     */
    public static function changePIN(string $phoneNumber, string $oldPIN, string $newPIN): array
    {
        $request = self::executePostRequest("/member/self/pin", [
            "oldPin" => $oldPIN,
            "newPin" => $newPIN,
            "confirmPin" => $newPIN
        ], $phoneNumber, $oldPIN);

        return $request->successful() ? $request->json() : [];
    }

    /**
     * Confirms the change of a PIN for the specified phone number.
     *
     * @param string $phoneNumber The phone number of the member.
     * @param string $oldPIN The old PIN to be replaced.
     * @param string $newPIN The new PIN to be set.
     * @param string $otp The One-Time Password for verification.
     *
     * @return array The JSON response from the API, or an empty array if the request was not successful.
     */
    public static function confirmChangePIN(string $phoneNumber, string $oldPIN, string $newPIN, string $otp): array
    {
        $request = self::executePostRequest("/member/self/pin", [
            "oldPin" => $oldPIN,
            "newPin" => $newPIN,
            "confirmPin" => $newPIN,
            "otp" => $otp
        ], $phoneNumber, $oldPIN);

        return $request->successful() ? $request->json() : [];
    }
}
