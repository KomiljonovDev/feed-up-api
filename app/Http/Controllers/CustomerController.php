<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function getToken()
    {
        $id = Customer::where('id', '>', '1')->latest()->first()?->id;
        $customer = Customer::create(['token'=>($id ?? 1) . "|" . Str::random(20)]);
        return response($customer);
    }
}

