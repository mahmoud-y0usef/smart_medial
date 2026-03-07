<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        
        $this->configureRateLimiting();
    }
    
    /**
     * Configure rate limiters for the medical platform.
     */
    protected function configureRateLimiting(): void
    {
        // WhatsApp webhook rate limiter
        RateLimiter::for('whatsapp', function (Request $request) {
            $phone = $request->input('entry.0.changes.0.value.messages.0.from', 'global');
            
            return Limit::perMinute(config('medical.rate_limits.whatsapp.per_phone', 60))
                ->by($phone)
                ->response(function () {
                    return response()->json([
                        'error' => 'Too many messages. Please wait a moment.',
                    ], 429);
                });
        });
        
        // AI operations rate limiter
        RateLimiter::for('ai', function (Request $request) {
            $clinicId = $request->user()?->clinic?->id ?? 'guest';
            
            return Limit::perMinute(config('medical.rate_limits.ai.per_clinic', 20))
                ->by($clinicId);
        });
        
        // Booking rate limiter
        RateLimiter::for('booking', function (Request $request) {
            $patientId = $request->user()?->patient?->id ?? $request->ip();
            
            return Limit::perMinute(config('medical.rate_limits.booking.per_patient', 10))
                ->by($patientId);
        });
        
        // General API rate limiter
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(config('medical.rate_limits.api.per_user', 100))
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}
