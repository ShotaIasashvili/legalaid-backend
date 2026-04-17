<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static string $routePath = '/dashboard';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return ! ($user instanceof User && $user->isHr());
    }
}