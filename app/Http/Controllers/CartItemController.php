<?php
namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartItemController extends Controller
{
    public function getMyCartItem (Request $request) {
        $attributes = $request->validate([
            'telegram_id'=>['required', 'integer'],
        ]);
        return response(CartItem::where('telegram_id', '=', $attributes['telegram_id'])->with('product')->get());
    }
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
