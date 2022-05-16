<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $roles, $permission = null)
    {
        $roles_array = explode('|', $roles);
        foreach($roles_array as $role){
            if ($request->user()->hasRole($role)){
                return $next($request);
            }
        } 

        if($permission !== null && !$request->user()->can($permission)) {

            abort(401, 'This action is unauthorized.');
        }

        return abort(401, 'This action is unauthorized.');

    }
}
