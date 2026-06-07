<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

abstract class AdminResource extends Resource
{
    public static function canAccess(): bool
    {
        return ! static::currentUser()?->isHr();
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canAccess();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canAccess();
    }

    public static function canDeleteAny(): bool
    {
        return static::canAccess();
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::canAccess();
    }

    public static function canForceDeleteAny(): bool
    {
        return static::canAccess();
    }

    public static function canReorder(): bool
    {
        return static::canAccess();
    }

    public static function canRestore(Model $record): bool
    {
        return static::canAccess();
    }

    public static function canRestoreAny(): bool
    {
        return static::canAccess();
    }

    public static function canView(Model $record): bool
    {
        return static::canAccess();
    }

    protected static function currentUser(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }
}