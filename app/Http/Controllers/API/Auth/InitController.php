<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InitController extends Controller
{
    public function init(){
        $auth = auth()->guard('api');

        return [
            'authenticated' => $auth->check(),
            'user' => $auth->check() ? $auth->user()->parse() : null
        ];
    }
}
