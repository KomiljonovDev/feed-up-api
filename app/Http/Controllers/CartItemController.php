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
     * @OA\Get(
     *     path="/cartItem/get",ar
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
            'telegram_id'=>['required', 'integer'],
        ]);
        return response(CartItem::where('telegram_id', '=', $attributes['telegram_id'])->with('product')->get());
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
            'telegram_id'=>['required', 'integer'],
            'product_id'=>['required', Rule::exists('products', 'id')],
            'quantity'=>['required', 'integer']
        ]);

        $cartItem = CartItem::where('telegram_id', '=', $attributes['telegram_id'])->where('product_id', '=', $attributes['product_id'])->get();

        if (!count($cartItem)){
            CartItem::create(['product_id'=>$attributes['product_id'], 'telegram_id'=>$attributes['telegram_id'], 'quantity'=>$attributes['quantity']]);
        }else{
            $cartItem[0]->increment('quantity', $attributes['quantity']);
            $cartItem[0]->save();
        }

        $cart = CartItem::where('telegram_id', '=', $attributes['telegram_id'])->get();
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
            'telegram_id'=>['required', 'integer'],
        ]);

        $cartItems = CartItem::where('telegram_id', '=', $attributes['telegram_id'])->where('product_id', '=', $product->id)->get();
        if ($cartItems->first()?->quantity <= 1){
            $cartItems->first()?->delete();
        }else{
            $cartItems[0]->decrement('quantity', 1);
            $cartItems[0]->save();
        }

        $cart = CartItem::where('telegram_id', '=', $attributes['telegram_id'])->get();
        return response($cart);
    }
}
