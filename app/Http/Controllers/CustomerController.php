<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/getToken",
     *     summary="Get customer token",
     *     tags={"Customer"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getToken()
    {
        $id = Customer::where('id', '>', '1')->latest()->first()?->id;
        $customer = Customer::create(['token'=>($id ?? 1) . "|" . Str::random(20)]);
        return response($customer);
    }
}

