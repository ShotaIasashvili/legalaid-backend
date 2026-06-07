[CmdletBinding()]
param(
    [string]$SiteUrl = 'https://www.new.legalaid.ge',
    [string]$BackendUrl = '',
    [string]$FrontendUrl = '',
    [string]$DatabaseHost = '',
    [string]$DatabasePort = '',
    [string]$DatabaseName = '',
    [string]$DatabaseUsername = '',
    [string]$DatabasePassword = '',
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

$frontendWorkspaceRoot = Join-Path (Split-Path -Parent $repoRoot) 'legalaid2'
$frontendDistPath = Join-Path $frontendWorkspaceRoot 'dist'
$legacyFrontendPublicPath = Join-Path $frontendWorkspaceRoot 'public'
$legacyFrontendTargets = @('seed-data', 'news-data', 'news-assets', 'legal-acts')

if ([string]::IsNullOrWhiteSpace($BackendUrl)) {
    $BackendUrl = $SiteUrl
}

if ([string]::IsNullOrWhiteSpace($FrontendUrl)) {
    $FrontendUrl = $SiteUrl
}

$normalizedBackendUrl = $BackendUrl.TrimEnd('/')
$normalizedFrontendUrl = $FrontendUrl.TrimEnd('/')
$backendHost = ([Uri]$normalizedBackendUrl).Host
$frontendHost = ([Uri]$normalizedFrontendUrl).Host

if (-not (Test-Path (Join-Path $frontendDistPath 'index.html'))) {
    throw "React frontend build not found at $frontendDistPath. Run 'npm run build' inside legalaid2 first."
}

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

function Get-EnvValue([string]$content, [string]$key, [string]$defaultValue = '') {
    $pattern = '(?m)^' + [regex]::Escape($key) + '=(.*)$'
    $match = [regex]::Match($content, $pattern)

    if (-not $match.Success) {
        return $defaultValue
    }

    $value = $match.Groups[1].Value.Trim()

    if ($value.Length -ge 2) {
        if (($value.StartsWith('"') -and $value.EndsWith('"')) -or ($value.StartsWith("'") -and $value.EndsWith("'"))) {
            return $value.Substring(1, $value.Length - 2)
        }
    }

    return $value
}

function Format-EnvValue([string]$value) {
    if ([string]::IsNullOrEmpty($value) -or $value -eq 'null' -or $value -notmatch '\s') {
        return $value
    }

    return '"' + $value.Replace('"', '\\"') + '"'
}

function Set-EnvValue([string]$content, [string]$key, [string]$value) {
    $formattedLine = $key + '=' + (Format-EnvValue $value)
    $pattern = '(?m)^' + [regex]::Escape($key) + '=.*$'

    if ([regex]::IsMatch($content, $pattern)) {
        return [regex]::Replace($content, $pattern, [System.Text.RegularExpressions.MatchEvaluator]{ param($match) $formattedLine }, 1)
    }

    return ($content.TrimEnd("`r", "`n") + "`r`n" + $formattedLine + "`r`n")
}

function New-AppKey() {
    $bytes = New-Object byte[] 32
    $random = [System.Security.Cryptography.RandomNumberGenerator]::Create()
    $random.GetBytes($bytes)
    $random.Dispose()

    return 'base64:' + [Convert]::ToBase64String($bytes)
}

function New-ZipFromDirectory([string]$sourceDirectory, [string]$destinationZip) {
    Add-Type -AssemblyName 'System.IO.Compression'
    Add-Type -AssemblyName 'System.IO.Compression.FileSystem'

    $sourceFullPath = [System.IO.Path]::GetFullPath($sourceDirectory)
    $destinationDirectory = Split-Path -Parent $destinationZip

    if (-not [string]::IsNullOrWhiteSpace($destinationDirectory)) {
        New-Directory $destinationDirectory
    }

    if (Test-Path $destinationZip) {
        Remove-Item $destinationZip -Force
    }

    $fileStream = [System.IO.File]::Open($destinationZip, [System.IO.FileMode]::CreateNew)

    try {
        $archive = New-Object System.IO.Compression.ZipArchive($fileStream, [System.IO.Compression.ZipArchiveMode]::Create, $false)

        try {
            Get-ChildItem -Path $sourceFullPath -Recurse -File | ForEach-Object {
                $relativePath = $_.FullName.Substring($sourceFullPath.Length).TrimStart('\', '/')
                $entryPath = $relativePath -replace '\\', '/'
                $entry = $archive.CreateEntry($entryPath, [System.IO.Compression.CompressionLevel]::Optimal)

                $entryStream = $entry.Open()

                try {
                    $inputStream = [System.IO.File]::OpenRead($_.FullName)

                    try {
                        $inputStream.CopyTo($entryStream)
                    }
                    finally {
                        $inputStream.Dispose()
                    }
                }
                finally {
                    $entryStream.Dispose()
                }
            }
        }
        finally {
            $archive.Dispose()
        }
    }
    finally {
        $fileStream.Dispose()
    }
}

New-Directory $distRoot
Reset-Path $buildRoot
Reset-Path $OutputZip
New-Directory $buildRoot
New-Directory $appRoot

Get-ChildItem -Path $frontendDistPath -Force | ForEach-Object {
    if ($_.Name -eq '.htaccess') {
        return
    }

    Copy-Item $_.FullName (Join-Path $buildRoot $_.Name) -Recurse -Force
}

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
Reset-Path (Join-Path $appRoot 'storage\app\install')
Reset-Path (Join-Path $appRoot 'storage\framework')
Reset-Path (Join-Path $appRoot 'storage\logs')
Reset-Path (Join-Path $buildRoot 'storage')

New-Directory (Join-Path $appRoot 'storage\app\private')
New-Directory (Join-Path $appRoot 'storage\app\install')
New-Directory (Join-Path $appRoot 'storage\framework\cache\data')
New-Directory (Join-Path $appRoot 'storage\framework\sessions')
New-Directory (Join-Path $appRoot 'storage\framework\views')
New-Directory (Join-Path $appRoot 'storage\logs')

Set-Content -Path (Join-Path $appRoot 'storage\app\install\.gitkeep') -Value '' -Encoding ASCII
Set-Content -Path (Join-Path $appRoot 'storage\app\private\.gitkeep') -Value '' -Encoding ASCII
Set-Content -Path (Join-Path $appRoot 'storage\framework\cache\data\.gitkeep') -Value '' -Encoding ASCII
Set-Content -Path (Join-Path $appRoot 'storage\framework\sessions\.gitkeep') -Value '' -Encoding ASCII
Set-Content -Path (Join-Path $appRoot 'storage\framework\views\.gitkeep') -Value '' -Encoding ASCII
Set-Content -Path (Join-Path $appRoot 'storage\logs\.gitkeep') -Value '' -Encoding ASCII

$rootPublicItems = @('css', 'img', 'js')
foreach ($item in $rootPublicItems) {
    $sourcePath = Join-Path $repoRoot (Join-Path 'public' $item)
    $destinationPath = Join-Path $buildRoot $item

    if (Test-Path $sourcePath) {
        Copy-Item $sourcePath $destinationPath -Recurse -Force
    }
}

$legacyFrontendBuildRoot = Join-Path $buildRoot 'legalaid2\public'
New-Directory $legacyFrontendBuildRoot

foreach ($target in $legacyFrontendTargets) {
    $sourcePath = $null

    foreach ($candidateRoot in @($legacyFrontendPublicPath, $frontendDistPath)) {
        $candidatePath = Join-Path $candidateRoot $target

        if (Test-Path $candidatePath) {
            $sourcePath = $candidatePath
            break
        }
    }

    if ($sourcePath) {
        $destinationPath = Join-Path $legacyFrontendBuildRoot $target
        Copy-Item $sourcePath $destinationPath -Recurse -Force
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

$envSourcePath = Join-Path $repoRoot '.env'
if (-not (Test-Path $envSourcePath)) {
    $envSourcePath = Join-Path $repoRoot '.env.example'
}

$envTemplate = Get-Content $envSourcePath -Raw
$resolvedAppKey = Get-EnvValue $envTemplate 'APP_KEY'

if ([string]::IsNullOrWhiteSpace($resolvedAppKey)) {
    $resolvedAppKey = New-AppKey
}

$resolvedDatabaseHost = if (-not [string]::IsNullOrWhiteSpace($DatabaseHost)) {
    $DatabaseHost
} elseif (-not [string]::IsNullOrWhiteSpace((Get-EnvValue $envTemplate 'DEPLOY_DB_HOST'))) {
    Get-EnvValue $envTemplate 'DEPLOY_DB_HOST'
} else {
    Get-EnvValue $envTemplate 'DB_HOST' 'localhost'
}

$resolvedDatabasePort = if (-not [string]::IsNullOrWhiteSpace($DatabasePort)) {
    $DatabasePort
} elseif (-not [string]::IsNullOrWhiteSpace((Get-EnvValue $envTemplate 'DEPLOY_DB_PORT'))) {
    Get-EnvValue $envTemplate 'DEPLOY_DB_PORT'
} else {
    Get-EnvValue $envTemplate 'DB_PORT' '3306'
}

$resolvedDatabaseName = if (-not [string]::IsNullOrWhiteSpace($DatabaseName)) {
    $DatabaseName
} elseif (-not [string]::IsNullOrWhiteSpace((Get-EnvValue $envTemplate 'DEPLOY_DB_DATABASE'))) {
    Get-EnvValue $envTemplate 'DEPLOY_DB_DATABASE'
} else {
    Get-EnvValue $envTemplate 'DB_DATABASE' ''
}

$resolvedDatabaseUsername = if (-not [string]::IsNullOrWhiteSpace($DatabaseUsername)) {
    $DatabaseUsername
} elseif (-not [string]::IsNullOrWhiteSpace((Get-EnvValue $envTemplate 'DEPLOY_DB_USERNAME'))) {
    Get-EnvValue $envTemplate 'DEPLOY_DB_USERNAME'
} else {
    Get-EnvValue $envTemplate 'DB_USERNAME' ''
}

$resolvedDatabasePassword = if (-not [string]::IsNullOrWhiteSpace($DatabasePassword)) {
    $DatabasePassword
} elseif (-not [string]::IsNullOrWhiteSpace((Get-EnvValue $envTemplate 'DEPLOY_DB_PASSWORD'))) {
    Get-EnvValue $envTemplate 'DEPLOY_DB_PASSWORD'
} else {
    Get-EnvValue $envTemplate 'DB_PASSWORD' ''
}

$envTemplate = Set-EnvValue $envTemplate 'APP_ENV' 'production'
$envTemplate = Set-EnvValue $envTemplate 'APP_DEBUG' 'false'
$envTemplate = Set-EnvValue $envTemplate 'APP_KEY' $resolvedAppKey
$envTemplate = Set-EnvValue $envTemplate 'APP_URL' $normalizedBackendUrl
$envTemplate = Set-EnvValue $envTemplate 'FRONTEND_URL' $normalizedFrontendUrl
$envTemplate = Set-EnvValue $envTemplate 'FRONTEND_PROD_URL' $normalizedFrontendUrl
$envTemplate = Set-EnvValue $envTemplate 'INSTALLER_ENABLED' 'true'
$envTemplate = Set-EnvValue $envTemplate 'DB_CONNECTION' 'mysql'
$envTemplate = Set-EnvValue $envTemplate 'DB_HOST' $resolvedDatabaseHost
$envTemplate = Set-EnvValue $envTemplate 'DB_PORT' $resolvedDatabasePort
$envTemplate = Set-EnvValue $envTemplate 'DB_DATABASE' $resolvedDatabaseName
$envTemplate = Set-EnvValue $envTemplate 'DB_USERNAME' $resolvedDatabaseUsername
$envTemplate = Set-EnvValue $envTemplate 'DB_PASSWORD' $resolvedDatabasePassword
$envTemplate = Set-EnvValue $envTemplate 'SESSION_DRIVER' 'file'
$envTemplate = Set-EnvValue $envTemplate 'QUEUE_CONNECTION' 'sync'
$envTemplate = Set-EnvValue $envTemplate 'CACHE_STORE' 'file'
$envTemplate = Set-EnvValue $envTemplate 'SESSION_DOMAIN' 'null'
$envTemplate = Set-EnvValue $envTemplate 'SESSION_SECURE_COOKIE' 'true'
$envTemplate = Set-EnvValue $envTemplate 'SESSION_SAME_SITE' 'lax'

$statefulDomains = (@($backendHost, $frontendHost) | Where-Object { -not [string]::IsNullOrWhiteSpace($_) } | Select-Object -Unique) -join ','

if ($statefulDomains) {
    $envTemplate = Set-EnvValue $envTemplate 'SANCTUM_STATEFUL_DOMAINS' $statefulDomains
}

Set-Content -Path (Join-Path $appRoot '.env') -Value $envTemplate -Encoding UTF8

$rootHtaccess = @'
<IfModule mod_dir.c>
    DirectoryIndex index.html index.php
</IfModule>

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Route the first-run installer through Laravel until installation completes.
    RewriteCond %{DOCUMENT_ROOT}/__APPDIR__/storage/app/install/installed !-f
    RewriteRule ^ index.php [L]

    # Keep Laravel endpoints on the PHP front controller.
    RewriteCond %{REQUEST_URI} ^/(admin|api|install|livewire|sanctum|storage|up|legacy-post-assets)(/.*)?$ [NC]
    RewriteRule ^ index.php [L]

    # Serve real files and directories directly.
    RewriteCond %{REQUEST_FILENAME} -d [OR]
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteRule ^ - [L]

    # When a React build is present, use it for public site routes.
    RewriteCond %{DOCUMENT_ROOT}/index.html -f
    RewriteRule ^ index.html [QSA,L]

    # Otherwise fall back to Laravel.
    RewriteRule ^ index.php [L]
</IfModule>
'@
$rootHtaccess = $rootHtaccess.Replace('__APPDIR__', $AppDirectoryName)
Set-Content -Path (Join-Path $buildRoot '.htaccess') -Value $rootHtaccess -Encoding ASCII

$uploadGuide = @"
Hostinger upload package

1. Upload this ZIP to public_html and extract it.
2. Keep the extracted structure exactly as-is: index.html, index.php, assets/, and the Laravel app all stay in public_html root.
3. Keep legalaid2/public in public_html too. The backend seeders and legacy post assets use it.
4. Set folder write permissions for $AppDirectoryName/storage and $AppDirectoryName/bootstrap/cache.
5. Open the main domain or /install in a browser.
6. Confirm the prefilled database details, then run the installer.
7. After the installer finishes, sign in at /admin and change the default admin password immediately.

Prepared site URL: $normalizedFrontendUrl
Admin panel URL: $normalizedFrontendUrl/admin
API base URL: $normalizedFrontendUrl/api/v1
Prepared database host: $resolvedDatabaseHost
Prepared database name: $resolvedDatabaseName
Prepared database user: $resolvedDatabaseUsername
"@
Set-Content -Path (Join-Path $buildRoot 'UPLOAD-FIRST.txt') -Value $uploadGuide -Encoding UTF8

Add-Type -AssemblyName 'System.IO.Compression.FileSystem'
New-ZipFromDirectory -sourceDirectory $buildRoot -destinationZip $OutputZip

Reset-Path $buildRoot

Write-Host "Created Hostinger package: $OutputZip"
