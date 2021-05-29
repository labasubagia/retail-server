<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}
