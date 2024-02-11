<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PaytabsWalletService
{
    private const BASE_URI = 'http://api.bsl.com.gh:8080/rest';

    public static function generateOTP(string $phoneNumber): bool
    {
        return self::executePostRequest(
            endpoint: '/members/registration/generate-otp',
            data: [
                'medium' => $phoneNumber,
                'actionType' => 'Registration',
                'otpToMobile' => true
            ]
        )->successful();
    }

    /**
     * Execute a POST request to the specified endpoint with the given data.
     *
     * @param string $endpoint The endpoint to send the POST request to.
     * @param array $data The data to include in the POST request.
     *
     * @return Response The response object obtained from the POST request.
     */
    private static function executePostRequest(string $endpoint, array $data, string $username = "", string $password = ""): Response
    {
        info("PaytabsWalletService Request", ['endpoint' => $endpoint, 'data', $data]);

        $response = Http::withBasicAuth($username, $password)->post(self::BASE_URI . $endpoint, $data);

        info("PaytabsWalletService Response", $response->json());

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

    public static function changePIN(string $oldPIN, string $newPIN)
    {
        $request = self::executePostRequest("/member/self/pin", [
            "oldPin" => $oldPIN,
            "newPin" => $newPIN,
            "confirmPin" => $newPIN
        ], "233249621938", "100589");

        return $request->successful() ? $request->json() : [];
    }

    public static function confirmChangePIN(string $oldPIN, string $newPIN, string $otp)
    {
        $request = self::executePostRequest("/member/self/pin", [
            "oldPin" => $oldPIN,
            "newPin" => $newPIN,
            "confirmPin" => $newPIN,
            "otp" => $otp
        ], "braasig@gmail.com", "100589");

        return $request->successful() ? $request->json() : [];
    }
}
