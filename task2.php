<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        // Eager load the related models to reduce the number of queries
        $orders = Order::with(['customer', 'items.product', 'cartItems'])
            ->get()
            ->map(function ($order) {
                // Calculate total amount and items count
                $totalAmount = $order->items->sum(function ($item) {
                    return $item->price * $item->quantity;
                });

                $itemsCount = $order->items->count();

                // Get the last added cart item creation date
                $lastAddedToCart = $order->cartItems
                    ->sortByDesc('created_at')
                    ->first()->created_at ?? null;

                // Check if a completed order exists
                $completedOrderExists = $order->status === 'completed';

                return [
                    'order_id' => $order->id,
                    'customer_name' => $order->customer->name,
                    'total_amount' => $totalAmount,
                    'items_count' => $itemsCount,
                    'last_added_to_cart' => $lastAddedToCart,
                    'completed_order_exists' => $completedOrderExists,
                    'created_at' => $order->created_at,
                ];
            });

        // Sort by completed_at for completed orders
        $orderData = $orders->sortByDesc(function ($order) {
            return Order::where('id', $order['order_id'])
                ->where('status', 'completed')
                ->value('completed_at');
        });

        return view('orders.index', ['orders' => $orderData]);
    }
}