<?php

namespace App\Http\Middleware;

use Closure;

class CheckProfessor
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
            if(session()->get('user')->type == 'professor') {
                return $next($request);
            }
        }

        return redirect(route('home.index'));
    }
}
