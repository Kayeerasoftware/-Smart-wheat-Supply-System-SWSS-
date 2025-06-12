<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the path the user should be redirected to after login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo(Request $request)
    {
        $user = Auth::user();
        switch ($user->role) {
            case 'farmer':
                return route('farmer.dashboard');
            case 'supplier':
                return route('supplier.dashboard');
            case 'manufacturer':
                return route('manufacturer.dashboard');
            case 'distributor':
                return route('distributor.dashboard');
            case 'retailer':
                return route('retailer.dashboard');
            default:
                return route('home');
        }
    }
}