<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Determine the path the user should be redirected to when not authenticated.
     */
    protected function redirectTo($request)
    {
         // إذا كان API → ما نرجع redirect أبداً
        if ($request->is('api/*')) {
            return null;
        }

        // للويب فقط
        return route('login');

    }
}
