<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request) {
        $attributes = $request->validate([
            'name'=>['required'],
            'email'=>['required', 'unique:users'],
            'password'=>['required']
        ]);

        $attributes['password'] = Hash::make($attributes['password']);

        $user = User::create($attributes);
        return response([
            'token'=>$user->createToken('oauth_token', ['*'], now()->addMonth())->plainTextToken
        ]);
    }
    public function login(Request $request) {
        $attributes = $request->validate([
            'email'=>['required'],
            'password'=>['required']
        ]);

        $user = User::where('email', $attributes['email'])->first();

        if (!$user || !Hash::check($attributes['password'], $user->password)){
            return response(['message'=>'The credentials are incorrect'], 401);
        }

        return response([
            'token'=>$user->createToken('oauth_token', ['*'], now()->addMonth())->plainTextToken
        ]);
    }
}

