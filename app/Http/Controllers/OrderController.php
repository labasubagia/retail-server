<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = Order::info()
            ->with('customer')
            ->when(
                $user->type == 'customer',
                fn ($query) => $query->where([
                    'created_by' => $user->id,
                    'customer_id' => $user->id,
                ])
            )
            ->when(
                $user->type == 'admin_store',
                fn ($query) => $query->where('store_id', $user->store_id)
            )
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    public function get(string $id)
    {
        $order = Order::with('customer')->info()->find($id);
        return response()->json(
            ['success' => true, 'order' => $order],
            $order ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }


    public function store(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'customer_id' => 'integer',
            'store_id' => 'required|integer',
            'products' => 'required|array',
            'products.*.id' => 'required|integer',
            'products.*.amount' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {

            // inventories
            $inventories = Inventory::where('store_id', $request->get('store_id'))
                ->whereIn('product_id', collect($request->products)->pluck('id'))
                ->info()
                ->get();

            // items payload
            $itemsPayload = collect($request->products)->map(function ($v) use ($inventories) {
                $product = $inventories->filter(fn ($i) => $i['product_id'] == $v['id'])->first();
                if (!$product) {
                    throw new Exception("Product not found");
                }
                $subtotal = $v['amount'] * $product->price;
                if ($v['amount'] > $product->stock) {
                    throw new Exception("Maximum buy $product->product_name is $product->stock");
                }
                return [
                    'amount' => $v['amount'],
                    'product_id' => $v['id'],
                    'at_time_price' => $product->price,
                    'subtotal_price' => $subtotal,
                ];
            });

            // order payload
            $orderPayload = $request->only((new Order)->getFillable());
            $orderPayload['created_by'] = $user->id;
            $orderPayload['total_price'] = $itemsPayload->sum('subtotal_price');
            if ($user->type == 'customer') {
                $orderPayload['customer_id'] = $user->id;
                $orderPayload['status'] = 'wait_delivery';
            }

            // create order
            $order = Order::create($orderPayload);
            $order->items()->createMany($itemsPayload);

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => Order::info()->findOrFail($order->id),
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    public function updateStatus(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|in:finished,wait_delivery,cancelled'
        ]);
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);
            $order->update(['status' => $request->get('status')]);
            DB::commit();
            return response()->json(
                ['success' => true, 'order' => $order],
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            DB::rollBack();
            return response(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
