<?php
namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartItemController extends Controller
{
    /**
     * @OA\Post(
     *     path="/cartItem/get",
     *     summary="Get cart item",
     *     tags={"Cart Item"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function getMyCartItem (Request $request) {
        $attributes = $request->validate([
            'token'=>['required', Rule::exists('customers', 'token')],
        ]);
        $customer = Customer::where('token', '=', $attributes['token'])->get()->first();
        return response(CartItem::where('customer_id', '=', $customer->id)->get());
    }
    /**
     * @OA\Post(
     *     path="/cartItem/create",
     *     summary="Create cart item",
     *     tags={"Cart Item"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "product_id", "quantity"},
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="quantity", type="integer")
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
            'product_id'=>['required', Rule::exists('products', 'id')],
            'quantity'=>['required', 'integer']
        ]);

        $customer = Customer::where('token', '=', $attributes['token'])->get()->first();
        $cartItem = CartItem::where('customer_id', '=', $customer->id)->where('product_id', '=', $attributes['product_id'])->get();

        if (!count($cartItem)){
            CartItem::create(['product_id'=>$attributes['product_id'], 'customer_id'=>$customer->id, 'quantity'=>$attributes['quantity']]);
        }else{
            $cartItem[0]->increment('quantity', $attributes['quantity']);
            $cartItem[0]->save();
        }

        $cart = CartItem::where('customer_id', '=', $customer->id)->get();
        return response($cart);
    }

    /**
     * @OA\Delete(
     *     path="/cartItem/{product:id}",
     *     summary="Delete cart item",
     *     tags={"Cart Item"},
     *     @OA\Parameter(
     *         name="product:id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
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
    public function destroy(Product $product, Request $request)
    {
        $attributes = $request->validate([
            'token'=>['required', Rule::exists('customers', 'token')],
        ]);

        $customer = Customer::where('token', '=', $attributes['token'])->get()->first();
        $cartItems = CartItem::where('customer_id', '=', $customer->id)->where('product_id', '=', $product->id)->get();
        if ($cartItems->first()?->quantity <= 1){
            $cartItems->first()?->delete();
        }else{
            $cartItems[0]->decrement('quantity', 1);
            $cartItems[0]->save();
        }

        $cart = CartItem::where('customer_id', '=', $customer->id)->get();
        return response($cart);
    }
}
