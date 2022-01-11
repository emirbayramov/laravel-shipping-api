<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, Closure $next)
    {
        $validator = Validator::make($request->all(),[
            'token' => 'string|required'
        ]);

        if($validator->fails()){
            return response()->json([
              'message'=>'Access denied'
            ]);
        }

        $api_token = $request->input('token');
        $user = User::where('token', $api_token)->first();

        if(!$user){
            return response()->json([
                'message'=>'Access denied'
            ]);
        }

        return $next($request);
    }
}

