<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request) {
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

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
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

