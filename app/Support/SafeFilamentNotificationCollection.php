<?php

namespace App\Support;

use Filament\Notifications\Collection;
use Filament\Notifications\Notification;

class SafeFilamentNotificationCollection extends Collection
{
    public static function fromLivewire($value): static
    {
        $notifications = collect(is_array($value) ? $value : [])
            ->filter(static fn (mixed $notification): bool => is_array($notification))
            ->map(static fn (array $notification): Notification => Notification::fromArray($notification))
            ->all();

        return app(static::class, ['items' => $notifications]);
    }
}
