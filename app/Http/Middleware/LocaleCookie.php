<?php

/*
    HelpRealm (dnyHelpRealm) developed by Arsen

    (C) 2019 - 2024 by Arsen

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Middleware;

use Closure;
use Session;
use Config;
use App;

class LocaleCookie
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
        //Set language according to cookie
        
        if(!Session::has('locale'))
        {
           Session::put('locale', Config::get('app.locale'));
        }

        App::setLocale(Session::get('locale'));

        return $next($request);
    }
}
