<?php
namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/orders",
     *     summary="Get orders",
     *     tags={"Orders"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function get()
    {
        $orders = Order::paginate(20);
        return response($orders);
    }

    /**
     * @OA\Post(
     *     path="/order/create",
     *     summary="Create order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'token'=>['required', Rule::exists('customers', 'token')],
        ]);

        $customer = Customer::where('token', '=', $attributes['token'])->get()->first();
        $cartItems = CartItem::where('customer_id', '=', $customer->id)->get();
        if ($cartItems){
            foreach ($cartItems as $cartItem){
                $product = Product::where('id', '=', $cartItem->product_id)->get()->first();
                Order::create([
                    'product_id'=>$product->id,
                    'product_name'=>$product->name,
                    'quantity'=>$cartItem->quantity,
                    'price'=>$cartItem->quantity*$product->price,
                    'status'=>'active'
                ]);
                Http::get('api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage?chat_id=931026030&text=Yangi buyurtma bor');
                $cartItem->delete();
            }
            return response(['message'=>'order sent successfully']);
        }
        return response(['message'=>'cart is emppty. Please fill cart first']);
    }

    /**
     * @OA\Patch(
     *     path="/orders/{order:id}/complete",
     *     summary="Complete order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="order:id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function complete(Order $order)
    {
        $order->status = 'completed';
        $order->save();
        return response($order);
    }

    /**
     * @OA\Patch(
     *     path="/orders/{order:id}/cancel",
     *     summary="Cancel order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="order:id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function cancel(Order $order)
    {
        $order->status = 'canceled';
        $order->save();
        return response($order);
    }

    /**
     * @OA\Delete(
     *     path="/orders/{order:id}",
     *     summary="Delete order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="order:id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return response(['message'=>'deleted succcessfully']);
    }
}
