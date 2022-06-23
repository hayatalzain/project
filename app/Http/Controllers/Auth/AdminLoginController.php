<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;

class AdminLoginController extends Controller
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

    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }


    public function username()
    {
      return 'username';
    }


    public function showLoginForm()
    {

        return view('auth.admin-login');
    }

    public function login( Request $request )
    {

        // Validate form data
       $data = $this->validate($request, [
            'username'     => 'required|min:4',
            'password'     => 'required|min:6'
        ]);

       $remember =  $request->get('remember',true);

        // Attempt to authenticate user
        // If successful, redirect to their intended location
     // $data = ['username' => $request->username, 'password' => $request->password];
      if ( Auth::guard('admin')->attempt($data, $remember)) {
           $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            redirect()->intended(route('admin'));
        }

        // Authentication failed, redirect back to the login form
         return $this->sendFailedLoginResponse($request);


    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {

        Auth::guard('admin')->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect()->guest(route( 'admin.login' ));
    }





}
