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
        $order->items->each(function ($item) use ($type) {

            if (strtolower($type) == 'decrement') {
                $item->inventory->decrement('stock', $item->amount);
            }

            if (strtolower($type) == 'increment') {
                $item->inventory->increment('stock', $item->amount);
            }
        });
    }
}
