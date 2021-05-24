<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['success' => true, 'brands' => Brand::all()]);
    }

    public function get(Request $request, string $id)
    {
        $brand = Brand::find($id);
        return response()->json(
            ['success' => true, 'brand' => $brand],
            $brand ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        try {
            $brand = new Brand;
            $brand->name = $request->input('name');
            $brand->save();
            return response()->json(['success' => true, 'brand' => $brand]);
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
            $brand = Brand::findOrFail($id);
            $brand->name = $request->input('name') ?? $brand->name;
            $brand->save();
            return response()->json(['success' => true, 'brand' => $brand]);
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
            Brand::destroy($id);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
