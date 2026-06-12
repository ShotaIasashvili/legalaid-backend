<?php

namespace App\Livewire;

use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class SafeFilamentPanelNotifications extends SafeFilamentNotifications
{
    public function getUser(): Model | Authenticatable | null
    {
        return Filament::auth()->user();
    }
}
