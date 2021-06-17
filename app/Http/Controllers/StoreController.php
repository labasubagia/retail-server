<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['success' => true, 'stores' => Store::all()]);
    }

    public function get(Request $request, string $id)
    {
        $store = Store::find($id);
        return response()->json(
            ['success' => true, 'store' => $store],
            $store ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }

    public function getLocations()
    {
        $locations = Store::select([
            DB::raw('COUNT(*) as count'),
            'address'
        ])
            ->groupBy('address')
            ->get();
        return response()->json($locations);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'address' => 'nullable',
        ]);
        try {
            $store = new Store;
            $store->name = $request->input('name');
            $store->address = $request->input('address');
            $store->save();
            return response()->json(['success' => true, 'store' => $store]);
        } catch (Exception $e) {
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $store = Store::findOrFail($id);
            $store->name = $request->input('name') ?? $store->name;
            $store->address = $request->input('address') ?? $store->address;
            $store->save();
            return response()->json(['success' => true, 'store' => $store]);
        } catch (Exception $e) {
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            Store::destroy($id);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
