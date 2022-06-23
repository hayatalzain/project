<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        logout as performLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function logout()
    {
        $this->performLogout(request());
        return redirect()->route('login');
    }


    public function username()
    {
      $username = request('username','');
      $column =   'username';
      if(filter_var($username, FILTER_VALIDATE_EMAIL))
      $column =   'email';

      request()->merge([$column => $username]);
      return $column;

    }


  protected function credentials()
  {
    return [
        $this->username() => request()->input($this->username()),
        'password' => request()->input('password'),
      //  'role' => 'admin',
        'status' => true,
    ];
  }





}
