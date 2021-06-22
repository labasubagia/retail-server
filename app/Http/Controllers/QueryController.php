<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryController extends Controller
{
    public function topSellingProduct(Request $request)
    {
        $this->validate($request, [
            'store_id' => 'required_without:address|exists:stores,id',
            'address' => 'required_without:store_id|exists:stores,address',
            'sort_by' => 'in:amount,subtotal_price',
            'limit' => 'integer',
        ]);
        $storeId = $request->get('store_id');
        $address = $request->get('address');
        $sortBy = $request->get('sort_by', 'amount');
        $limit = $request->get('limit', 20);

        $result = Product::join('inventories', 'inventories.product_id', 'products.id')
            ->join('stores', 'stores.id', 'inventories.store_id')
            ->join('order_items', 'order_items.inventory_id', 'inventories.id')
            ->whereNotIn('order_items.order_id', $this->getCancelledOrderIds())
            ->when($storeId, fn ($q) => $q->where('stores.id', $storeId))
            ->when($address, fn ($q) => $q->where('stores.address', $address))
            ->select([
                'products.*',
                'products.image as image_url',
                DB::raw("SUM(order_items.amount) as amount"),
                DB::raw("SUM(order_items.subtotal_price) as subtotal_price"),
                DB::raw('SUM(order_items.subtotal_price)/SUM(order_items.amount) as price'),
            ])
            ->groupBy('product_id')
            ->orderBy($sortBy, 'DESC')
            ->take($limit)
            ->get();
        return response()->json($result);
    }

    public function topSellStore(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $limit = $request->get('limit', 5);

        // get store with most order
        $stores = Store::join('orders', 'orders.store_id', 'stores.id')
            ->whereNotIn('orders.id', $this->getCancelledOrderIds())
            ->whereYear('orders.created_at', $year)
            ->select([
                'stores.*',
                DB::raw('SUM(orders.total_price) as total_price')
            ])
            ->groupBy('stores.id')
            ->orderBy('total_price', 'DESC')
            ->take($limit)
            ->get();

        // orders based on store
        $orders = Order::whereIn('store_id', $stores->pluck('id'))
            ->whereNotIn('orders.id', $this->getCancelledOrderIds())
            ->join('order_items', 'order_items.order_id', 'orders.id')
            ->groupBy('store_id')
            ->select([
                'store_id',
                DB::raw('SUM(order_items.amount) as amount')
            ])
            ->get();

        // use amount in products
        foreach ($stores as $store) {
            foreach ($orders as $order) {
                if ($store->id == $order->store_id) {
                    $store->amount += (int)$order->amount;
                }
            }
        }

        return response()->json($stores);
    }

    public function compareSellingTwoProduct(Request $request)
    {
        $this->validate($request, [
            'product_id_1' => 'required|exists:products,id',
            'product_id_2' => 'required|exists:products,id',
        ]);

        $cancelOrderIds = $this->getCancelledOrderIds();

        $getProductSales = function ($id) use ($cancelOrderIds) {
            return Product::where('products.id', $id)
                ->whereNotIn('order_id', $cancelOrderIds)
                ->join('inventories', 'inventories.product_id', 'products.id')
                ->join('order_items', 'order_items.inventory_id', 'inventories.id')
                ->select([
                    'products.*',
                    DB::raw('SUM(order_items.amount) as amount')
                ])
                ->groupBy('products.id')
                ->first();
        };

        $product1 = $getProductSales($request->get('product_id_1'));
        $product2 = $getProductSales($request->get('product_id_2'));

        $result = [
            'difference' => abs($product1->amount - $product2->amount),
            'products' => [
                $product1,
                $product2,
            ],
        ];

        return response()->json($result);
    }

    public function outSellProductStore(Request $request)
    {
        $this->validate($request, [
            'product_id_1' => 'required|exists:products,id',
            'product_id_2' => 'required|exists:products,id',
        ]);

        $id1 = $request->get('product_id_1');
        $id2 = $request->get('product_id_2');

        // get product with sell amount
        $getSellProducts = fn ($storeId, $productIds) =>
        Product::whereIn('products.id', $productIds)
            ->where('stores.id', $storeId)
            ->join('inventories', 'inventories.product_id', 'products.id')
            ->join('order_items', 'order_items.inventory_id', 'inventories.id')
            ->join('stores', 'stores.id', 'inventories.store_id')
            ->select([
                'inventories.product_id',
                'products.*',
                DB::raw("SUM(order_items.amount) as amount"),
            ])
            ->groupBy('product_id')
            ->get()
            ->values();

        // loop to get products and compare it
        $stores = Store::all()
            ->map(function ($store) use ($getSellProducts, $id1, $id2) {
                $products = $getSellProducts($store->id, [$id1, $id2]);

                $product1 = collect($products)->firstWhere('product_id', $id1);
                $sell1 = (int)($product1->amount ?? 0);

                $product2 = collect($products)->firstWhere('product_id', $id2);
                $sell2 = (int)($product2->amount ?? 0);

                $store->p1_sell = $sell1;
                $store->p2_sell = $sell2;
                $store->is_outsell = $sell1 > $sell2;
                return $store;
            });
        // ->filter(fn ($store) => $store->is_outsell);

        return response()->json($stores);
    }

    public function topBuyWithinProduct(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:products,id',
            'limit' => 'integer'
        ]);

        $id = $request->get('product_id');
        $limit = $request->get('limit', 3);

        // get order ids where productId is included
        $orderIds = Order::join('order_items', 'order_items.order_id', 'orders.id')
            ->join('inventories', 'inventories.id', 'order_items.inventory_id')
            ->whereNotIn('orders.id', $this->getCancelledOrderIds())
            ->where('inventories.product_id', $id)
            ->pluck('orders.id');

        // get all other product ids that buy with productId
        $items = OrderItem::join('inventories', 'inventories.id', 'order_items.inventory_id')
            ->whereIn('order_id', $orderIds)
            ->where('inventories.product_id', '!=', $id)
            ->select([
                'inventories.product_id',
                'order_items.amount',
                'order_items.subtotal_price',
            ])
            ->get();

        // get all other products
        $products = Product::whereIn('products.id', $items->pluck('product_id'))
            ->info()
            ->get();

        // use amount & price in products
        foreach ($items as $item) {
            foreach ($products as $product) {
                if ($item->product_id == $product->id) {
                    $product->amount += $item->amount;
                    $product->subtotal_price += $item->subtotal_price;
                }
            }
        }

        $products = $products->sortByDesc('amount')
            ->take($limit)
            ->values();

        return response()->json($products);
    }

    private function getCancelledOrderIds()
    {
        return Order::where('status', 'cancelled')->pluck('id');
    }
}
