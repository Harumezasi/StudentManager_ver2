<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ResponseObject;
use Closure;

class CheckAdmin
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
        if(session()->has('user')) {
            if(session()->get('user')->type == 'admin') {
                return $next($request);
            }
        }

        //return redirect(route('home.index'))->with('alert', '허가되지 않은 접근입니다.');
        return response()->json(new ResponseObject(
            false, '허가되지 않은 접근입니다.'
        ), 401);
    }
}
