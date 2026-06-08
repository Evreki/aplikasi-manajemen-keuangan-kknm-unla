<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class CustomLogin extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.custom-login';
    
    // We override the layout so we don't inherit Filament's base structure, 
    // allowing full control over <html>, <head>, and <body> from the layout.
    protected static string $layout = 'layouts.auth';
}
