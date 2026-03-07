<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckClinicAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/clinic/login');
        }

        $user = auth()->user();
        
        if ($user->role !== UserRole::Doctor && $user->role !== UserRole::Receptionist) {
            // Redirect to user's appropriate panel
            if ($user->isAdmin()) {
                return redirect('/admin');
            }
            if ($user->isPharmacy()) {
                return redirect('/pharmacy');
            }
            
            abort(403, 'ليس لديك صلاحية للوصول إلى أي لوحة');
        }

        return $next($request);
    }
}
