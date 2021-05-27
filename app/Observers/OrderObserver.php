<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

trait OrderObserver
{
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($order) {

            if (in_array($order->getOriginal('status'), ['cancelled'])) {
                static::updateItemStock($order, 'decrement');
            }

            if (in_array($order->status, ['cancelled'])) {
                static::updateItemStock($order, 'increment');
            }
        });
    }

    private static function updateItemStock(Order $order, string $type)
    {
        $order->items->each(function ($item) use ($order, $type) {

            $inventory = Inventory::where([
                'store_id' => $order->store_id,
                'product_id' => $item->product_id,
            ])->first();

            if (strtolower($type) == 'decrement') {
                $inventory->decrement('stock', $item->amount);
            }

            if (strtolower($type) == 'increment') {
                $inventory->increment('stock', $item->amount);
            }
        });
    }
}
