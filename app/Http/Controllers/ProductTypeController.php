<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductTypeController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['success' => true, 'product_types' => ProductType::all()]);
    }

    public function get(Request $request, string $id)
    {
        $productType = ProductType::find($id);
        return response()->json(
            ['success' => true, 'product_type' => $productType],
            $productType ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        try {
            $productType = new ProductType;
            $productType->name = $request->input('name');
            $productType->save();
            return response()->json(['success' => true, 'product_type' => $productType]);
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
            $productType = ProductType::findOrFail($id);
            $productType->name = $request->input('name') ?? $productType->name;
            $productType->save();
            return response()->json(['success' => true, 'product_type' => $productType]);
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
            ProductType::destroy($id);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
