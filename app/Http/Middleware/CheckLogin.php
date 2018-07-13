<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\ResponseObject;

class CheckLogin
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
        if(!session()->has('user')) {
            return response()->json(new ResponseObject(
                false, __('response.not_authorized')
            ), 200);
        }

        return $next($request);
    }
}
