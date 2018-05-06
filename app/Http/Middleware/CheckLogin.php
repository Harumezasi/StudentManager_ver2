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
                false, "허가되지 않은 접근입니다."
            ), 200);
        }

        return $next($request);
    }
}
