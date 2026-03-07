<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPharmacyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/pharmacy/login');
        }

        $user = auth()->user();
        
        if ($user->role !== UserRole::Pharmacy) {
            // Redirect to user's appropriate panel
            if ($user->isAdmin()) {
                return redirect('/admin');
            }
            if ($user->isDoctor() || $user->isReceptionist()) {
                return redirect('/clinic');
            }
            
            abort(403, 'ليس لديك صلاحية للوصول إلى أي لوحة');
        }

        return $next($request);
    }
}
