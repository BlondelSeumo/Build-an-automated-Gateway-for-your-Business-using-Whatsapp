<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class isVerifiedLicense
{

    private  $verified;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */


    public function handle(Request $request, Closure $next)
    {
        $allowedRoute = ['setting.install_app', 'activateLicense', 'connectDB', 'settings.install_app'];
        if (!in_array($request->route()->getName(), $allowedRoute) && !env('APP_INSTALLED')) {
            return redirect()->route('setting.install_app');
        }
        if ($request->route()->getName() == 'settings.install_app' || $request->route()->getName() == 'setting.install_app' || $request->route()->getName() == 'activateLicense' || $request->route()->getName() == 'connectDB') {
            return $next($request);
        }
		Cache::put('verifiedlicense', true, 1800);
    }
}
