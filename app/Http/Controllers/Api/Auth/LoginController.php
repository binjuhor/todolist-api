<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $error) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $error->errors(),
            ], 500);
        }

        if (!auth()->attempt($credentials)) {
            return response([
                'message' => 'Your credentials are invalid.',
            ], 401);
        }

        $user = auth()->user();

        return (new UserResource($user))->additional([
            'token' => $user->createToken('myapptoken')->plainTextToken
        ]);
    }
}
