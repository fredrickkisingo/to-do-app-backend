<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as Validator;


class RegisterController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [ 
            'email_address' => 'required|unique:users|string|max:255',
            'password' => 'required|min:4'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $user = User::create([
            'email_address' => $request->email_address,
            'password' => Hash::make($request->password)
        ]);

         // return $this->login($request);
         $success['token'] =  $user->createToken('authToken')-> plainTextToken;
         $success['email_address'] =  $user->email_address;

         return $this->login($request);

 
         return response()->json(['success'=>$success,'message'=>'Account created successfully.'], 200);
    }
}
