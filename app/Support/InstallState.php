<?php

namespace App\Support;

class InstallState
{
    public static function isInstalled(): bool
    {
        return is_file(self::markerPath());
    }

    public static function markerPath(): string
    {
        return (string) config('installer.marker_path', storage_path('app/install/installed'));
    }

    public static function ensureDirectoryExists(): void
    {
        $directory = dirname(self::markerPath());

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    public static function markInstalled(array $metadata = []): void
    {
        self::ensureDirectoryExists();

        file_put_contents(
            self::markerPath(),
            json_encode([
                'installed_at' => now()->toIso8601String(),
                'metadata' => $metadata,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    public static function clear(): void
    {
        $markerPath = self::markerPath();

        if (is_file($markerPath)) {
            unlink($markerPath);
        }
    }
}