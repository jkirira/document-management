<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Transformers\Admin\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $token = Auth::user()->createToken('login-token');
            return response()->json([
                'success' => true,
                'token' => $token->plainTextToken,
                'user' => (new UserTransformer())->transform(Auth::user()),
            ], Response::HTTP_OK);
        }

        return response()->json([
            'error_message' => 'The provided credentials do not match our records.',
        ], Response::HTTP_UNAUTHORIZED);

    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
            return response()->json([], Response::HTTP_NO_CONTENT);

        } else {
            return response()->json(['error_message' => 'Logout Error'], Response::HTTP_BAD_REQUEST);
        }

    }

    public function user (Request $request) {
        $user_id = $request->user_id;

        if ($request->user()->id !== $user_id) {
            return response()->json(['error_message' => 'Invalid Request'], 500);
        }

        return (new UserTransformer())->transform($request->user());

    }

}
