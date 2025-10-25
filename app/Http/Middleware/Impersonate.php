<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Impersonate
{
  public function handle(Request $request, Closure $next)
  {
    if (session()->has('impersonate_user_id')) {
      auth()->onceUsingId(session('impersonate_user_id')); // solo para esta request
    }
    return $next($request);
  }
}