<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventories = Inventory::info()
            ->when(
                $request->get('store_id'),
                fn ($query) => $query->where('store_id', $request->get('store_id'))
            )
            ->get();
        return response()->json(['success' => true, 'inventories' => $inventories]);
    }

    public function get(string $id)
    {
        $inventory = Inventory::info()->find($id);
        return response()->json(
            ['success' => true, 'inventory' => $inventory],
            $inventory ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'product_id' => 'required',
            'store_id' => 'required',
            'vendor_id' => 'required',
        ]);
        try {
            $isExist = Inventory::where('store_id', $request->user()->store_id)
                ->where('product_id', $request->product_id)
                ->first();
            if ($isExist) throw new Exception('Product already exists');

            $payload = $request->only((new Inventory)->getFillable());
            $inventory = Inventory::create($payload);
            return response()->json(['success' => true, 'inventory' => $inventory]);
        } catch (Exception $e) {
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'price' => 'integer|min:0',
            'stock' => 'integer|min:0',
        ]);
        try {
            $inventory = Inventory::findOrFail($id);
            $payload = $request->only((new Inventory)->getFillable());
            $inventory->update($payload);
            return response()->json(['success' => true, 'inventory' => $inventory]);
        } catch (Exception $e) {
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy(string $id)
    {
        try {
            Inventory::destroy($id);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
