<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserCheckController extends Controller
{
    public function isAdminRetail(Request $request)
    {
        return $request->user()->type == 'admin_retail';
    }

    public function isAdminStore(Request $request)
    {
        return $request->user()->type == 'admin_store';
    }

    public function isCustomer(Request $request)
    {
        return $request->user()->type == 'customer';
    }
}
