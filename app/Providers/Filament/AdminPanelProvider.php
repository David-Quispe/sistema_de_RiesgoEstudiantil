<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AlertasRecientesWidget;
use App\Filament\Widgets\RiesgoCarreraWidget;
use App\Filament\Widgets\RiesgoStatsWidget;
use App\Filament\Widgets\TendenciaRiesgoWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()

            // ── Identidad visual SMER ──────────────────────────────────────
            ->brandName('SMER — TECSUP')
            ->colors([
                'primary'  => Color::Blue,
                'danger'   => Color::Red,
                'warning'  => Color::Amber,
                'success'  => Color::Green,
                'info'     => Color::Sky,
            ])

            // ── Modelo de autenticación ────────────────────────────────────
            ->authGuard('web')

            // ── Recursos, páginas y widgets (autodescubrimiento) ───────────
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )

            // ── Página de inicio ──────────────────────────────────────────
            ->pages([
                Pages\Dashboard::class,
            ])

            // ── Widgets del dashboard (orden controlado por $sort en cada clase)
            ->widgets([
                RiesgoStatsWidget::class,      // sort=1 — tarjetas KPI
                AlertasRecientesWidget::class, // sort=2 — tabla de alertas
                TendenciaRiesgoWidget::class,  // sort=3 — gráfico de líneas
                RiesgoCarreraWidget::class,    // sort=4 — barras por carrera
            ])

            // ── Grupos de navegación ──────────────────────────────────────
            ->navigationGroups([
                NavigationGroup::make('Seguimiento')
                    ->icon('heroicon-o-clipboard-document-check'),

                NavigationGroup::make('Gestión')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),

                NavigationGroup::make('Administración')
                    ->icon('heroicon-o-shield-check')
                    ->collapsed(),
            ])

            // ── Middleware ────────────────────────────────────────────────
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
            ]);
    }
}
