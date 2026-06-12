<?php

namespace App\Livewire;

use App\Support\SafeFilamentNotificationCollection;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Livewire\Component;

class SafeFilamentNotifications extends Component
{
    public bool $isFilamentNotificationsComponent = true;

    public SafeFilamentNotificationCollection $notifications;

    public static Alignment $alignment = Alignment::Right;

    public static VerticalAlignment $verticalAlignment = VerticalAlignment::Start;

    public static ?string $authGuard = null;

    public function mount(): void
    {
        $this->notifications = new SafeFilamentNotificationCollection;
        $this->pullNotificationsFromSession();
    }

    #[On('notificationsSent')]
    public function pullNotificationsFromSession(): void
    {
        foreach (session()->pull('filament.notifications') ?? [] as $notification) {
            if (! is_array($notification)) {
                continue;
            }

            $this->pushNotification(Notification::fromArray($notification));
        }
    }

    #[On('notificationSent')]
    public function pushNotificationFromEvent(array $notification): void
    {
        $this->pushNotification(Notification::fromArray($notification));
    }

    #[On('notificationClosed')]
    public function removeNotification(string $id): void
    {
        if (! $this->notifications->has($id)) {
            return;
        }

        $this->notifications->forget($id);
    }

    public function handleBroadcastNotification(array $notification): void
    {
        if (($notification['format'] ?? null) !== 'filament') {
            return;
        }

        $this->pushNotification(Notification::fromArray($notification));
    }

    protected function pushNotification(Notification $notification): void
    {
        $this->notifications->put($notification->getId(), $notification);
    }

    public function getUser(): Model | Authenticatable | null
    {
        return auth(static::$authGuard)->user();
    }

    public function getBroadcastChannel(): ?string
    {
        $user = $this->getUser();

        if (! $user) {
            return null;
        }

        if (method_exists($user, 'receivesBroadcastNotificationsOn')) {
            return $user->receivesBroadcastNotificationsOn();
        }

        $userClass = str_replace('\\', '.', $user::class);

        return "{$userClass}.{$user->getKey()}";
    }

    public static function alignment(Alignment $alignment): void
    {
        static::$alignment = $alignment;
    }

    public static function verticalAlignment(VerticalAlignment $alignment): void
    {
        static::$verticalAlignment = $alignment;
    }

    public static function authGuard(?string $guard): void
    {
        static::$authGuard = $guard;
    }

    public function render(): View
    {
        return view('filament-notifications::notifications');
    }
}
