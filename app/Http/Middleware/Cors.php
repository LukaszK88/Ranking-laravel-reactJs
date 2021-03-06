<?php

namespace App\Http\Middleware;

use Closure;


class Cors
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
            return $next($request)
                ->header('Cache-Control' , 'public')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Access-Control-Allow-Headers, Authorization, X-Requested-With')
                ->header('Access-Control-Expose-Headers', 'Authorization')
                ->header('Access-Control-Allow-Methods', 'GET, HEAD, PATCH, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Credentials', 'true');

        }



}
