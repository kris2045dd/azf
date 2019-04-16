<?php

namespace App\Http\Middleware;

use Closure;

class BackendLoginIpRestriction
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
		if (env('BACKEND_IP_WHITELIST_ENABLE')) {
			$ip = $request->ip();
			$whitelists = explode(',', env('BACKEND_IP_WHITELIST'));
			if (! in_array($ip, $whitelists)) {
				return response("Your IP isn't in whitelist. ({$ip})", 403);
				//abort(403, "Your IP isn't in whitelist. ({$ip})");
			}
		}
        return $next($request);
    }
}
