<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getTitle(): string|Htmlable
    {
        return 'Ingresar al sistema';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    protected function getCardHeaderContent(): Htmlable|string|null
    {
        return null;
    }
}
