<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
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
            ->login(Login::class)

            // ── Identidad visual TECSUP ────────────────────────────────────
            ->brandName('SMER')
            ->brandLogo('https://www.tecsup.edu.pe/wp-content/uploads/2024/07/Group-680.png')
            ->brandLogoHeight('2rem')
            ->colors([
                // Azul corporativo TECSUP #1e3a5f
                'primary' => [
                     50 => '236, 242, 248',
                    100 => '207, 222, 237',
                    200 => '163, 191, 218',
                    300 => '116, 158, 197',
                    400 => '74, 128, 177',
                    500 => '44, 100, 153',
                    600 => '30, 74, 120',
                    700 => '22, 58, 95',
                    800 => '15, 42, 70',
                    900 => '9, 27, 47',
                    950 => '5, 15, 27',
                ],
                'danger'  => Color::Red,
                'warning' => Color::Amber,
                'success' => Color::Green,
                'info'    => Color::Sky,
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

            // ── Página de inicio ───────────────────────────────────────────
            ->pages([
                Pages\Dashboard::class,
            ])

            // ── Widgets del dashboard ──────────────────────────────────────
            ->widgets([
                RiesgoStatsWidget::class,
                AlertasRecientesWidget::class,
                TendenciaRiesgoWidget::class,
                RiesgoCarreraWidget::class,
            ])

            // ── Grupos de navegación ───────────────────────────────────────
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

            // ── Middleware ─────────────────────────────────────────────────
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
