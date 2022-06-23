<?php

namespace App\Http\Middleware;

use Closure;
use App;

class SetLocalLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      // if($request->is('admin') || $request->is('admin/*'))
      // $locale = 'en';

      // else{
       $locale =  session('lang','ar');
     //  }
        App::setLocale($locale);
        return $next($request);
    }
}
