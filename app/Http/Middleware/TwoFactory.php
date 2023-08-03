<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;

class TwoFactory
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if(auth()->check() && $user->otp_code){
            if(!$request->is('verify*')){
                return $this->apiResponse(null, 'you must verify the code first', 400);
            }
        }
        return $next($request);
    }
}
