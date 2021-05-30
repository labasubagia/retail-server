<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\OrderItem;

trait OrderItemObserver
{
    protected static function boot()
    {
        parent::boot();

        static::created(function ($order) {
            static::updateStock($order);
        });

        static::updated(function ($order) {
            static::updateStock($order);
        });
    }

    private static function updateStock(OrderItem $item)
    {
        $order = $item->order;
        if (!$order) return;

        if (in_array($order->status, ['finished', 'wait_delivery'])) {
            $item->inventory->decrement('stock', $item->amount);
        }
    }
}
