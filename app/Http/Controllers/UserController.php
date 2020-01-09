<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
     public function authenticate(Request $request)
        {
            $credentials = $request->only('email', 'password');

            $user = User::where('email', $request->email)->first();

            // check if the user doesn't exist
            if(! $user){

                return response()->json([
                "status" => "error",
                'error' => "User doesn't exist"
                 ]);
            }

            // get token
            if (Auth::attempt($credentials)) {

                $token = JWTAuth::attempt($credentials);
                return response()->json([
                	"status" => "success",
                	"data" => [
                        "token" => $token,
                        "user" => $user
                    ]
                ], 200);

            }


            return response()->json([
                "status" => "error",
                'error' => 'invalid_credentials'
            ]);
            
    }
}
