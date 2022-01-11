<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_auth')->except('login', 'register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'login'    => 'string|required',
            'password' => 'string|required'
        ]);

        if($validator->fails()){
            return response()->json([
                'message'=>'Wrong data'
            ],500);
        }

        $login = $request->input('login');
        $password = $request->input('password');

        //check if login already exists
        $userCount = User::where('login', $login)->count();

        if($userCount>0){
            return response()->json([
                'message'=>'User already exists'
            ],500);
        }

        User::createUser($login, $password);

        return response()->json([
            'login'   => $login,
            'created' => true
        ]);

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'login'    => 'string|required',
            'password' => 'string|required'
        ]);

        if($validator->fails()){
            return response()->json([
                'message'=>'Wrong data'
            ],500);
        }

        $login = $request->input('login');
        $password = $request->input('password');

        if(User::check($login, $password)){
            $user = User::where('login', $login)->first();
            $user->token = Str::random(60);

            $user->save();

            return [
              'token' => $user->token
            ];
        }

        return response()->json([
            'message'=>'Wrong login or password'
        ],500);
    }

    public function logout(Request $request)
    {
        $token = $request->input('token');

        $user = User::where('token', $token)->first();

        $user->token = null;

        $user->save();

        return [
            'logout' => $user->login
        ];
    }

}
