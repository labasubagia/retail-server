<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct()
    {
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ]);

        try {
            $user = User::where('email', $request->input('email'))->first();
            $isSame = Hash::check($request->input('password'), $user->password);
            if (!$isSame) throw new Exception('wrong email or password');
            $user->api_token = base64_encode(Str::random(40));
            $user->save();
            return response()->json(['success' => true, 'user' => $user]);
        } catch (Exception $e) {
            return response()->json(
                ['success' => false, 'error' => $e->getMessage()],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'name' => 'required',
            'type' => 'required|in:admin_retail,admin_store,customer'
        ]);
        try {
            $payload = $request->only((new User)->getFillable());
            $payload['password'] = Hash::make($payload['password']);
            $user = User::create($payload);
            return response()->json(['success' => true, 'user' => $user]);
        } catch (Exception $e) {
            return response()->json(
                ['success' => false, 'error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function current(Request $request)
    {
        $user = $request->user();
        return response()->json(['success' => (bool)$user, 'user' => $user]);
    }
}
