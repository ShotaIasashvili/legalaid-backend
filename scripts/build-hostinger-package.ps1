[CmdletBinding()]
param(
    [string]$BackendUrl = 'https://powderblue-rhinoceros-805295.hostingersite.com',
    [string]$FrontendUrl = 'https://lightgreen-caterpillar-796045.hostingersite.com',
    [string]$OutputZip = '',
    [string]$AppDirectoryName = 'laravel'
)

$ErrorActionPreference = 'Stop'

$repoRoot = Split-Path -Parent $PSScriptRoot
$distRoot = Join-Path $repoRoot 'dist'
$buildRoot = Join-Path ([System.IO.Path]::GetTempPath()) 'legalaid-hostinger-public_html'
$appRoot = Join-Path $buildRoot $AppDirectoryName

if ([string]::IsNullOrWhiteSpace($OutputZip)) {
    $OutputZip = Join-Path $distRoot 'backend-hostinger-public_html-ready.zip'
}

$normalizedFrontendUrl = $FrontendUrl.TrimEnd('/')
$backendHost = ([Uri]$BackendUrl).Host
$frontendHost = ([Uri]$normalizedFrontendUrl).Host
$legacyFrontendPublicPath = Join-Path (Join-Path (Split-Path -Parent $repoRoot) 'legalaid2') 'public'
$legacyFrontendTargets = @('seed-data', 'news-data', 'news-assets', 'legal-acts')

$copyTargets = @(
    'app',
    'bootstrap',
    'config',
    'database',
    'public',
    'resources',
    'routes',
    'storage',
    'vendor',
    'artisan',
    'composer.json',
    'composer.lock',
    '.env.example',
    'phpunit.xml',
    'README.md',
    'HOSTINGER_DEPLOY.md'
)

function Reset-Path([string]$path) {
    if (Test-Path $path) {
        Remove-Item $path -Recurse -Force
    }
}

function New-Directory([string]$path) {
    if (-not (Test-Path $path)) {
        New-Item -ItemType Directory -Path $path -Force | Out-Null
    }
}

New-Directory $distRoot
Reset-Path $buildRoot
Reset-Path $OutputZip
New-Directory $buildRoot
New-Directory $appRoot

foreach ($target in $copyTargets) {
    $sourcePath = Join-Path $repoRoot $target
    $destinationPath = Join-Path $appRoot $target

    if (-not (Test-Path $sourcePath)) {
        continue
    }

    Copy-Item $sourcePath $destinationPath -Recurse -Force
}

Reset-Path (Join-Path $appRoot '.git')
Reset-Path (Join-Path $appRoot '.qodo')
Reset-Path (Join-Path $appRoot 'database\database.sqlite')
Reset-Path (Join-Path $appRoot 'storage\framework')
Reset-Path (Join-Path $appRoot 'storage\logs')
Reset-Path (Join-Path $buildRoot 'storage')

New-Directory (Join-Path $appRoot 'storage\app\private')
New-Directory (Join-Path $appRoot 'storage\framework\cache\data')
New-Directory (Join-Path $appRoot 'storage\framework\sessions')
New-Directory (Join-Path $appRoot 'storage\framework\views')
New-Directory (Join-Path $appRoot 'storage\logs')

Set-Content -Path (Join-Path $appRoot 'storage\app\private\.gitkeep') -Value '' -Encoding ASCII
Set-Content -Path (Join-Path $appRoot 'storage\framework\cache\data\.gitkeep') -Value '' -Encoding ASCII
Set-Content -Path (Join-Path $appRoot 'storage\framework\sessions\.gitkeep') -Value '' -Encoding ASCII
Set-Content -Path (Join-Path $appRoot 'storage\framework\views\.gitkeep') -Value '' -Encoding ASCII
Set-Content -Path (Join-Path $appRoot 'storage\logs\.gitkeep') -Value '' -Encoding ASCII

$rootPublicItems = @('css', 'img', 'js', '.htaccess')
foreach ($item in $rootPublicItems) {
    $sourcePath = Join-Path $repoRoot (Join-Path 'public' $item)
    $destinationPath = Join-Path $buildRoot $item

    if (Test-Path $sourcePath) {
        Copy-Item $sourcePath $destinationPath -Recurse -Force
    }
}

if (Test-Path $legacyFrontendPublicPath) {
    $legacyFrontendBuildRoot = Join-Path $buildRoot 'legalaid2\public'
    New-Directory $legacyFrontendBuildRoot

    foreach ($target in $legacyFrontendTargets) {
        $sourcePath = Join-Path $legacyFrontendPublicPath $target
        $destinationPath = Join-Path $legacyFrontendBuildRoot $target

        if (Test-Path $sourcePath) {
            Copy-Item $sourcePath $destinationPath -Recurse -Force
        }
    }
}

$frontController = @'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists(
    $maintenance = __DIR__ . '/__APPDIR__/storage/framework/maintenance.php'
)) {
    require $maintenance;
}

require __DIR__ . '/__APPDIR__/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/__APPDIR__/bootstrap/app.php';

$app->handleRequest(Request::capture());
'@
$frontController = $frontController.Replace('__APPDIR__', $AppDirectoryName)
Set-Content -Path (Join-Path $buildRoot 'index.php') -Value $frontController -Encoding ASCII

$envTemplate = Get-Content (Join-Path $repoRoot '.env.example') -Raw
$envTemplate = $envTemplate -replace '(?m)^APP_ENV=.*$', 'APP_ENV=production'
$envTemplate = $envTemplate -replace '(?m)^APP_DEBUG=.*$', 'APP_DEBUG=false'
$envTemplate = $envTemplate -replace '(?m)^APP_URL=.*$', "APP_URL=$BackendUrl"
$envTemplate = $envTemplate -replace '(?m)^FRONTEND_URL=.*$', "FRONTEND_URL=$normalizedFrontendUrl"
$envTemplate = $envTemplate -replace '(?m)^FRONTEND_PROD_URL=.*$', "FRONTEND_PROD_URL=$normalizedFrontendUrl"
$envTemplate = $envTemplate -replace '(?m)^SESSION_DRIVER=.*$', 'SESSION_DRIVER=file'
$envTemplate = $envTemplate -replace '(?m)^QUEUE_CONNECTION=.*$', 'QUEUE_CONNECTION=sync'
$envTemplate = $envTemplate -replace '(?m)^CACHE_STORE=.*$', 'CACHE_STORE=file'
$envTemplate = $envTemplate -replace '(?m)^SESSION_DOMAIN=.*$', 'SESSION_DOMAIN=null'

if ($envTemplate -notmatch '(?m)^SESSION_SECURE_COOKIE=') {
    $envTemplate += "`r`nSESSION_SECURE_COOKIE=true"
}

if ($envTemplate -notmatch '(?m)^SESSION_SAME_SITE=') {
    $envTemplate += "`r`nSESSION_SAME_SITE=lax"
}

if ($envTemplate -notmatch '(?m)^SANCTUM_STATEFUL_DOMAINS=') {
    $envTemplate += "`r`nSANCTUM_STATEFUL_DOMAINS=$backendHost,$frontendHost"
}

Set-Content -Path (Join-Path $appRoot '.env') -Value $envTemplate -Encoding UTF8

$uploadGuide = @"
Hostinger upload package

1. Upload this ZIP to public_html and extract it.
2. Edit $AppDirectoryName/.env and fill in DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD, and APP_KEY.
3. Keep the extracted structure exactly as-is: index.php stays in public_html root and the Laravel app stays in the $AppDirectoryName folder.
4. If the package includes legalaid2/public, keep that folder in public_html too. The backend seeders and legacy post assets use it.
5. Set folder write permissions for $AppDirectoryName/storage and $AppDirectoryName/bootstrap/cache.
6. If you have SSH access, run: php $AppDirectoryName/artisan key:generate --force
7. If you have SSH access, run: php $AppDirectoryName/artisan migrate --force
8. If you have SSH access, run: php $AppDirectoryName/artisan db:seed --force

Prepared backend URL: $BackendUrl
Prepared frontend origin: $normalizedFrontendUrl
"@
Set-Content -Path (Join-Path $buildRoot 'UPLOAD-FIRST.txt') -Value $uploadGuide -Encoding UTF8

Add-Type -AssemblyName 'System.IO.Compression.FileSystem'
[System.IO.Compression.ZipFile]::CreateFromDirectory(
    $buildRoot,
    $OutputZip,
    [System.IO.Compression.CompressionLevel]::Optimal,
    $false
)

Reset-Path $buildRoot

Write-Host "Created Hostinger package: $OutputZip"
