<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\users\User;
use Carbon\Carbon;

class UserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if( Auth::check() ){
            $expiresAt = Carbon::now()->addMinutes(1); // keep online for 1 min
            Cache::put('is_online'.Auth::user()->id, true , $expiresAt);
            //last seen
            User::where('id' , Auth::user()->id)->update(['last_seen'=>Carbon::now()]);
        }
        return $next($request);
    }
}
