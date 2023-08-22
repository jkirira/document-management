<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $token = Auth::user()->createToken('login-token');
            return response()->json([
                'success' => true,
                'token' => $token->plainTextToken,
            ], Response::HTTP_OK);
        }

        return response()->json([
            'email' => 'The provided credentials do not match our records.',
        ], Response::HTTP_UNAUTHORIZED);

    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
            return response()->json([], Response::HTTP_NO_CONTENT);

        } else {
            return response()->json(['error' => 'Logout Error'], Response::HTTP_BAD_REQUEST);
        }

    }

}
