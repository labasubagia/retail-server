<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getAllByType(Request $request)
    {
        $users = User::when(
            $request->get('type'),
            fn ($q) => $q->where('type', $request->get('type'))
        )
            ->get();
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(
                ['success' => false, 'error' => 'User not found'],
                Response::HTTP_NOT_FOUND
            );
        };
        $this->validate($request, [
            'email' => "email|unique:users,email,$user->email",
            'name' => 'string',
            'type' => 'string|in:admin_retail,admin_store,customer',
            'store_id' => 'required_if:type,==,admin_store',
        ]);
        try {
            $payload = $request->only((new User)->getFillable());
            if ($request->password) {
                $payload['password'] = Hash::make($payload['password']);
            }
            $updated = $user->update($payload);
            return response()->json(['success' => $updated, 'user' => $user]);
        } catch (Exception $e) {
            return response()->json(
                ['success' => false, 'error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy($id)
    {
        try {
            $isSuccess = (bool)User::destroy($id);
            return response()->json(['success' => $isSuccess]);
        } catch (Exception $e) {
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
