<?php

namespace App\Http\Controllers;

use App\Support\InstallState;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class InstallController extends Controller
{
    public function show(Request $request): View
    {
        return view('install', [
            'defaults' => [
                'app_url' => (string) config('app.url', $request->getSchemeAndHttpHost()),
                'db_host' => (string) config('database.connections.mysql.host', 'localhost'),
                'db_port' => (string) config('database.connections.mysql.port', '3306'),
                'db_database' => (string) config('database.connections.mysql.database', ''),
                'db_username' => (string) config('database.connections.mysql.username', ''),
                'db_password' => (string) config('database.connections.mysql.password', ''),
            ],
            'installInfo' => $request->session()->get('install_info'),
            'installed' => InstallState::isInstalled(),
        ]);
    }

    public function install(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_url' => ['required', 'url'],
            'db_host' => ['required', 'string', 'max:255'],
            'db_port' => ['required', 'integer', 'between:1,65535'],
            'db_database' => ['required', 'string', 'max:255'],
            'db_username' => ['required', 'string', 'max:255'],
            'db_password' => ['nullable', 'string', 'max:255'],
        ]);

        $appKey = (string) config('app.key');

        if ($appKey === '') {
            $appKey = 'base64:'.base64_encode(Encrypter::generateKey(Config::string('app.cipher')));
        }

        $environmentValues = [
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_KEY' => $appKey,
            'APP_URL' => rtrim($validated['app_url'], '/'),
            'FRONTEND_URL' => rtrim($validated['app_url'], '/'),
            'FRONTEND_PROD_URL' => rtrim($validated['app_url'], '/'),
            'INSTALLER_ENABLED' => 'true',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $validated['db_host'],
            'DB_PORT' => (string) $validated['db_port'],
            'DB_DATABASE' => $validated['db_database'],
            'DB_USERNAME' => $validated['db_username'],
            'DB_PASSWORD' => $validated['db_password'] ?? '',
            'SESSION_DRIVER' => 'file',
            'QUEUE_CONNECTION' => 'sync',
            'CACHE_STORE' => 'file',
            'SESSION_DOMAIN' => 'null',
            'SESSION_SECURE_COOKIE' => 'true',
            'SESSION_SAME_SITE' => 'lax',
        ];

        try {
            $this->applyRuntimeConfiguration($environmentValues);
            DB::connection('mysql')->getPdo();

            $this->writeEnvironmentValues($environmentValues);

            Artisan::call('optimize:clear');
            $this->applyRuntimeConfiguration($environmentValues);

            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();

            Artisan::call('db:seed', ['--force' => true]);
            $seedOutput = Artisan::output();

            InstallState::markInstalled([
                'app_url' => $environmentValues['APP_URL'],
                'db_host' => $environmentValues['DB_HOST'],
                'db_database' => $environmentValues['DB_DATABASE'],
            ]);

            return redirect()
                ->route('install.show')
                ->with('install_info', [
                    'admin_email' => 'admin@legalaid.ge',
                    'admin_password' => 'LegalAid@2026!',
                    'app_url' => $environmentValues['APP_URL'],
                    'admin_url' => rtrim($environmentValues['APP_URL'], '/').'/admin',
                    'migrate_output' => trim($migrateOutput),
                    'seed_output' => trim($seedOutput),
                ]);
        } catch (Throwable $exception) {
            Log::error('Installation failed.', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return redirect()
                ->route('install.show')
                ->withErrors([
                    'install' => 'Installation failed: '.$exception->getMessage(),
                ])
                ->withInput();
        }
    }

    protected function applyRuntimeConfiguration(array $environmentValues): void
    {
        config([
            'app.key' => $environmentValues['APP_KEY'],
            'app.url' => $environmentValues['APP_URL'],
            'app.frontend_url' => $environmentValues['FRONTEND_URL'],
            'app.frontend_prod_url' => $environmentValues['FRONTEND_PROD_URL'],
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $environmentValues['DB_HOST'],
            'database.connections.mysql.port' => $environmentValues['DB_PORT'],
            'database.connections.mysql.database' => $environmentValues['DB_DATABASE'],
            'database.connections.mysql.username' => $environmentValues['DB_USERNAME'],
            'database.connections.mysql.password' => $environmentValues['DB_PASSWORD'],
            'session.driver' => 'file',
            'cache.default' => 'file',
            'queue.default' => 'sync',
        ]);

        DB::purge('mysql');
    }

    protected function writeEnvironmentValues(array $environmentValues): void
    {
        $environmentPath = app()->environmentFilePath();
        $environmentContents = file_exists($environmentPath)
            ? file_get_contents($environmentPath)
            : '';

        foreach ($environmentValues as $key => $value) {
            $formattedLine = $key.'='.$this->formatEnvironmentValue($value);
            $pattern = "/^{$key}=.*$/m";

            if (preg_match($pattern, $environmentContents) === 1) {
                $environmentContents = preg_replace($pattern, $formattedLine, $environmentContents) ?? $environmentContents;
                continue;
            }

            $environmentContents = rtrim($environmentContents).PHP_EOL.$formattedLine.PHP_EOL;
        }

        file_put_contents($environmentPath, $environmentContents);
    }

    protected function formatEnvironmentValue(string $value): string
    {
        if ($value === '' || $value === 'null' || preg_match('/\s/', $value) !== 1) {
            return $value;
        }

        return '"'.str_replace('"', '\\"', $value).'"';
    }
}