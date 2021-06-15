<?php

namespace App\Http\Controllers;

use App\Helpers\UploadHelper;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{

    private $imagePath = 'images/product';

    public function index()
    {
        $products = Product::info()->get();
        return response()->json(['success' => true, 'products' => $products]);
    }

    public function get(string $id)
    {
        $product = Product::info()->find($id);
        return response()->json(
            ['success' => true, 'product' => $product],
            $product ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'barcode' => 'required',
            'product_type_id' => 'required',
            'brand_id' => 'required',
            'image' => 'required|image',
        ]);
        try {
            $payload = $request->only((new Product)->getFillable());
            $payload['image'] = UploadHelper::imgBB($request->file('image'));
            $product = Product::create($payload);
            return response()->json(
                ['success' => true, 'product' => $product],
                Response::HTTP_CREATED
            );
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
            'image' => 'image',
        ]);
        try {
            $product = Product::findOrFail($id);
            $payload = $request->only((new Product)->getFillable());
            if ($request->hasFile('image')) {
                $payload['image'] = UploadHelper::imgBB($request->file('image'));
            }
            $product->update($payload);
            return response()->json(['success' => true, 'product' => $product]);
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
            $product = Product::findOrFail($id);
            $product->delete();

            $old = "$this->imagePath/$product->image";
            if (file_exists($old) && is_file($old)) unlink($old);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
