<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckPharmacyAccess;
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

class PharmacyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('pharmacy')
            ->path('pharmacy')
            ->viteTheme('resources/css/filament/pharmacy/theme.css')
            ->login(Login::class)
            ->brandName('الشفاء الذكي - لوحة الصيدلية')
            
            ->darkModeBrandLogo(url('images/logo/primary.svg'))
            ->discoverClusters(in: app_path('Filament/Pharmacy/Clusters'), for: 'App\\Filament\\Pharmacy\\Clusters')
            ->discoverResources(in: app_path('Filament/Pharmacy/Resources'), for: 'App\\Filament\\Pharmacy\\Resources')
            ->discoverPages(in: app_path('Filament/Pharmacy/Pages'), for: 'App\\Filament\\Pharmacy\\Pages')
            ->discoverWidgets(in: app_path('Filament/Pharmacy/Widgets'), for: 'App\\Filament\\Pharmacy\\Widgets')
            ->navigationGroups([
                'الروشتات',
                'المخزون',
                'إدارة الصيدلية',
            ])
            ->userMenuItems([
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label('الملف الشخصي')
                    ->url(fn (): string => \App\Filament\Pharmacy\Pages\EditProfile::getUrl())
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
                CheckPharmacyAccess::class,
            ])
            ->authGuard('web')
            ->authPasswordBroker('users')
            ->spa()
            ->colors([
                'primary' => Color::Emerald,
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
            fn (): string => '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet"><meta name="viewport" content="width=device-width, initial-scale=1.0"><script>document.documentElement.setAttribute("dir","rtl");</script><style>html,body,#app,[x-data]{direction:rtl !important;text-align:right !important;}html{direction:rtl !important;}body{direction:rtl !important;}aside[class*="fi-sidebar"],aside.fi-sidebar,.fi-sidebar{left:auto !important;right:0 !important;direction:rtl !important;}[class*="fi-"]{direction:rtl !important;}</style>'
        );
        
        FilamentView::registerRenderHook(
            'panels::body.start',
            fn (): string => '<script>if(document.body)document.body.setAttribute("dir","rtl");</script>'
        );
    }
}
