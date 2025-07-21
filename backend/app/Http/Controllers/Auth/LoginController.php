<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return makeApiResponse(null, 'Invalid credentials.', false, 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return makeApiResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful.');
    }
}
