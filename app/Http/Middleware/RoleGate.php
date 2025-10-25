<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleGate {
  public function handle($req, Closure $next, ...$roles){
    $user = $req->user();
    if(!$user || !in_array($user->role, $roles)) abort(403);
    return $next($req);
  }
}