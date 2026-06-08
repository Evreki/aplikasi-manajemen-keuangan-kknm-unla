<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CustomDashboard extends Page
{
    protected static string $view = 'filament.pages.custom-dashboard';

    protected static string $layout = 'layouts.dashboard';

    protected static string $routePath = '/';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    protected static ?int $navigationSort = -2;

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }
}
