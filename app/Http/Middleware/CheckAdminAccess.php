<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/admin/login');
        }

        $user = auth()->user();
        
        if ($user->role !== UserRole::Admin) {
            // Redirect to user's appropriate panel
            if ($user->isDoctor() || $user->isReceptionist()) {
                return redirect('/clinic');
            }
            if ($user->isPharmacy()) {
                return redirect('/pharmacy');
            }
            
            abort(403, 'ليس لديك صلاحية للوصول إلى أي لوحة');
        }

        return $next($request);
    }
}
