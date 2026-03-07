<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckClinicAccess;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use App\Enums\UserRole;

class ClinicPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('clinic')
            ->path('clinic')
            ->viteTheme('resources/css/filament/clinic/theme.css')
            ->login(Login::class)
            ->brandName(function () {
                $clinic = auth()->check() ? (auth()->user()->clinic ?? auth()->user()->clinicEmployer) : null;
                return $clinic ? $clinic->name : 'الشفاء الذكي - لوحة العيادة';
            })
            
            ->darkModeBrandLogo(url('images/logo/primary.svg'))
            ->discoverClusters(in: app_path('Filament/Clinic/Clusters'), for: 'App\\Filament\\Clinic\\Clusters')
            ->discoverResources(in: app_path('Filament/Clinic/Resources'), for: 'App\\Filament\\Clinic\\Resources')
            ->discoverPages(in: app_path('Filament/Clinic/Pages'), for: 'App\\Filament\\Clinic\\Pages')
            ->discoverWidgets(in: app_path('Filament/Clinic/Widgets'), for: 'App\\Filament\\Clinic\\Widgets')
            ->navigationGroups([
                'قائمة الانتظار',
                'الكشوفات',
                'الروشتات',
                'إدارة العيادة',
            ])
            ->userMenuItems([
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label('الملف الشخصي')
                    ->url(fn (): string => \App\Filament\Clinic\Pages\EditProfile::getUrl())
                    ->icon('heroicon-o-user-circle'),
            ])
            ->databaseNotifications()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                CheckClinicAccess::class,
            ])
            ->authGuard('web')
            ->authPasswordBroker('users')
            ->spa()
            ->colors([
                'primary' => Color::Teal,
            ])
            ->font('Cairo')
            ->renderHook(
                'panels::body.start',
                fn (): string => Blade::render('<div style="display:none;"></div>')
            );
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::head.start',
            fn (): string => '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet"><meta name="viewport" content="width=device-width, initial-scale=1.0"><script>document.documentElement.setAttribute("dir","rtl");</script><style>html,body,#app,[x-data]{direction:rtl !important;text-align:right !important;}html{direction:rtl !important;}body{direction:rtl !important;}aside[class*="fi-sidebar"],aside.fi-sidebar,.fi-sidebar{left:auto !important;right:0 !important;direction:rtl !important;}[class*="fi-"]{direction:rtl !important;}[x-data*="sidebar"]{direction:rtl !important;}button[x-show*="sidebar"],button[aria-label*="Toggle sidebar"],button.fi-sidebar-open-btn,[class*="sidebar-open"]{justify-content:flex-start !important;padding-right:1rem !important;}@media (max-width: 768px){.fi-topbar{flex-direction:row-reverse !important;}.fi-topbar > div:first-child{margin-right:auto !important;margin-left:0 !important;}}</style>'
        );
        
        FilamentView::registerRenderHook(
            'panels::body.start',
            fn (): string => '<script>if(document.body)document.body.setAttribute("dir","rtl");</script>'
        );
    }
}
