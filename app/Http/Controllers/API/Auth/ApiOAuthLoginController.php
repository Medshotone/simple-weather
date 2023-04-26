<?php

namespace App\Http\Controllers\API\Auth;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class ApiOAuthLoginController extends ApiAuthController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function googleLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors()->toArray());
        }

        $inputs = $validator->validate();

        try {
            $googleUser = Socialite::driver('google')->userFromToken($inputs['access_token']);
        } catch (ClientException $e) {
            $response = $e->getResponse();

            return $this->sendError(
                'Invalid Credentials', json_decode($response->getBody(), true), $response->getStatusCode()
            );
        }

        $user = $this->userRepository->getUserByEmail((string)$googleUser->email);

        if (!$user) {
            $user = $this->userRepository->createUser([
                'name' => (string)$googleUser->name,
                'email' => (string)$googleUser->email,
                'provider_id' => (int)$googleUser->id,
                'password' => (string)$googleUser->email,
            ]);
        }

        $success['token'] = $user->createToken('weather')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User login successfully.');
    }
}
