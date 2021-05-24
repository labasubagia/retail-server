<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['success' => true, 'vendors' => Vendor::all()]);
    }

    public function get(Request $request, string $id)
    {
        $vendor = Vendor::find($id);
        return response()->json(
            ['success' => true, 'vendor' => $vendor],
            $vendor ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'address' => 'nullable',
        ]);
        try {
            $vendor = new Vendor;
            $vendor->name = $request->input('name');
            $vendor->address = $request->input('address');
            $vendor->save();
            return response()->json(['success' => true, 'vendor' => $vendor]);
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
            $vendor = Vendor::findOrFail($id);
            $vendor->name = $request->input('name') ?? $vendor->name;
            $vendor->address = $request->input('address') ?? $vendor->address;
            $vendor->save();
            return response()->json(['success' => true, 'vendor' => $vendor]);
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
            Vendor::destroy($id);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
