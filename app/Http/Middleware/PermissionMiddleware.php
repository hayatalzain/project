<?php

namespace App\Http\Middleware;

use Closure;
 
class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$permission)
    {
      //dd($permission);  
     if($request->user()->hasPermission($permission))
         return $next($request);
         return back();


   // $permissions_array = explode('|', $permissions);
    // $user = $this->auth->user();
    foreach($permissions_array as $permission){
        if(!$request->user()->hasPermission($permission)){
            return $next($request);
        }
    }

    return redirect()->back();

  }


}
