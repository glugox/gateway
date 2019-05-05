<?php

namespace App\Http\Middleware;

use Closure;
use function PHPSTORM_META\type;

class CorsMiddleware
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
        if($request->getMethod() == 'OPTIONS'){

            //die("POK");
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Request-Headers: *');
            header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Key, Authorization');
            header('Access-Control-Allow-Credentials: true');
            exit(0);
        }

        return $next($request)
              ->header('Access-Control-Allow-Origin', '*')
              ->header('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,PATCH,OPTIONS')
              ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

    }
}
