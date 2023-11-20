<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Product;
use App\Models\ContractListProduct;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterRequest;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login a user
     *
     * @param LoginRequest $request
     * @return array
     */
    public function login(LoginRequest $request): array
    {
        if(!$user = User::checkUserCredentials($request->email, $request->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        return [
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }

    /**
     * Register a user
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

        // For quicker testing add some contract_list_product records for the user
        $data = [];
        $products = Product::limit(100)->get();
        foreach($products as $product) {
            $data[] = [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'price' => random_int(100, 200)
            ];
        }
        ContractListProduct::insert($data);

        return new UserResource($user);
    }
}
