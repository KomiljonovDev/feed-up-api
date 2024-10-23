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
    public function complete (OrderItem $order) {
        $order->status = 'completed';
        $order->save();
        return response($order);
    }
    public function cancel (OrderItem $order) {
        $order->status = 'canceled';
        $order->save();
        return response($order);
    }
    public function destroy (OrderItem $order) {
        $order->delete();
        return response(['message' => 'deleted succcessfully']);
    }
}
