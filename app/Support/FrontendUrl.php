<?php

namespace App\Support;

class FrontendUrl
{
    public static function app(string $path = '/'): string
    {
        return self::join(self::appBase(), $path);
    }

    public static function angular(string $path = '/'): string
    {
        return self::join(self::angularBase(), $path);
    }

    public static function shouldServeAngularShell(): bool
    {
        return self::normalizeBase(self::appBase()) === self::normalizeBase(self::angularBase());
    }

    public static function appBase(): string
    {
        $configured = config('app.url') ?: 'http://127.0.0.1:8000';

        if (app()->environment('local') && in_array($configured, ['http://localhost', 'https://localhost'], true)) {
            return 'http://127.0.0.1:8000';
        }

        return rtrim($configured, '/');
    }

    public static function angularBase(): string
    {
        $configured = config('app.angular_url');

        if (is_string($configured) && $configured !== '') {
            return rtrim($configured, '/');
        }

        return app()->environment('local')
            ? 'http://127.0.0.1:4200'
            : self::appBase();
    }

    private static function join(string $base, string $path): string
    {
        return rtrim($base, '/').'/'.ltrim($path, '/');
    }

    private static function normalizeBase(string $url): string
    {
        return strtolower(rtrim($url, '/'));
    }
}
