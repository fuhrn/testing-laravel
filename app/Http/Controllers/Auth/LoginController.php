<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
//        Auth::attempt(request(['email', 'password']));
        if (! Auth::attempt(request(['email', 'password']))) {
            return redirect('/login')->withErrors([
                'email' => ['These credentials do not math our records'],
            ]);
        }
        Auth::attempt(request(['email', 'password']));
        return redirect('/backstage/concerts');
    }
}
