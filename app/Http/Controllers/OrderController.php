<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function index () {
        $orders = OrderResource::collection(Order::all());
        return response($orders);
    }
    public function show (Order $order) {
        $order->load('orderItems');
        $order->load('orderItems.product');
        return new OrderResource($order);
    }
    public function store (Request $request) {
        $attributes = $request->validate(['telegram_id' => 'required|integer', 'phone_number' => 'required|integer', 'full_name' => 'required|string',]);

        $cartItems = CartItem::where('telegram_id', '=', $attributes['telegram_id'])->get();
        if ($cartItems) {
            $order_id = Order::create(['phone_number' => $attributes['phone_number'], 'full_name' => $attributes['full_name']])->id;
            foreach ($cartItems as $cartItem) {
                $product = Product::where('id', '=', $cartItem->product_id)->get()->first();
                OrderItem::create(['order_id' => $order_id, 'product_id' => $product->id, 'quantity' => $cartItem->quantity, 'price' => $cartItem->quantity * $product->price]);
                Http::get('api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage?chat_id=931026030&text=Yangi buyurtma bor');
                $cartItem->delete();
            }
            return response(['message' => 'order sent successfully']);
        }
        return response(['message' => 'cart is emppty. Please fill cart first']);
    }

    public function complete (Order $order) {
        $order->status = 'completed';
        $order->save();
        $order->load('orderItems');
        $order->load('orderItems.product');
        return new OrderResource($order);
    }
    public function cancel (Order $order) {
        $order->status = 'canceled';
        $order->save();
        $order->load('orderItems');
        $order->load('orderItems.product');
        return new OrderResource($order);
    }
    public function destroy (OrderItem $order) {
        $order->delete();
        return response(['message' => 'deleted successfully']);
    }

    public function stats () {
        $completed = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, MONTHNAME(created_at) as month_name, COUNT(*) as count')
            ->where('status', 'completed')
            ->groupByRaw('year, month, month_name')
            ->orderByRaw('month')
            ->pluck('count', 'month_name');

        $all = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, MONTHNAME(created_at) as month_name, COUNT(*) as count')
            ->groupByRaw('year, month, month_name')
            ->orderByRaw('month')
            ->pluck('count', 'month_name');

        $data = [
            'completed' => $completed,
            'all' => $all
        ];

        return response($data);
    }
    public function latest () {
        $latest =OrderResource::collection(Order::query()->where('status', 'pending')->orderBy('created_at', 'desc')->limit(5)->get());
        return response($latest);
    }
}
