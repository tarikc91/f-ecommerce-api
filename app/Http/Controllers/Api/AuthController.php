<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterRequest;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user
     *
     * @param LoginRequest $request
     * @return array
     */
    public function login(LoginRequest $request): array
    {
        $user = User::checkUserCredentials($request->email, $request->password);

        if(!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token');

        return [
            'token' => $token->plainTextToken
        ];
    }

    /**
     * Register user
     *
     * @param RegisterRequest $request
     * @return UserResource
     */
    public function register(RegisterRequest $request): UserResource
    {
        $user = User::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password),
        ]);

        return new UserResource($user);
    }
}
