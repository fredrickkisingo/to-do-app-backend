<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class LoginController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|unique:users|string|max:255',
            'password' => 'required|min:4'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

       

         return $this->login($request);

     }
    public function login(Request $request){

        $user = User::where('email', $request->get('email'))
        ->first();
    if (!$user) {
        return response()->json(['message'=> 'User does not exist'],200);
    }

    $new_request = Request::create('oauth/token', 'POST', [
        'client_id' => env('API_PASSPORT_CLIENT_ID'),
        'client_secret' => env('API_PASSPORT_CLIENT_SECRET'),
        'username' => $request->email,
        'password' => $request->password,
        'grant_type' => 'password',
        'scope' => '*'
    ]);

    $new_request->headers->set('Origin', '*');

    return app()->handle($new_request);

    }

    public function logout(Request $request){
    // Get user who requested the logout

    \Log::info($request->all());
        
    \Log::info('Logout request from user: ' . auth()->guard('api')->user());
          auth()->guard('api')->user()->token()->revoke();

        return response()->json(["message" => "Logged out."]);
    }
}
