<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class RequireAdmin
{
    public function handle(Request $request, Closure $next, string $role = null)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }
        if ($role && !auth()->user()->hasAdminRole($role)) {
            abort(403);
        }
        return $next($request);
    }
}
