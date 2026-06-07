# Legal Aid Laravel Backend — Windows Setup Script
# Usage: .\setup.ps1 [-DBName legalaid] [-DBUser root] [-DBPassword ""] [-AppURL http://localhost:8000]
#
# Requirements: PHP 8.2+, Composer, MySQL/MariaDB running

param(
    [string]$DBName     = "legalaid",
    [string]$DBUser     = "root",
    [string]$DBPassword = "",
    [string]$DBHost     = "127.0.0.1",
    [string]$DBPort     = "3306",
    [string]$AppURL     = "http://localhost:8000",
    [switch]$SkipSeed   = $false
)

$ErrorActionPreference = "Stop"

function Write-Step($msg)  { Write-Host "`n$msg" -ForegroundColor Cyan }
function Write-Ok($msg)    { Write-Host "  ✓ $msg"  -ForegroundColor Green }
function Write-Warn($msg)  { Write-Host "  ⚠ $msg"  -ForegroundColor Yellow }
function Write-Fail($msg)  { Write-Host "`n✗ $msg`n" -ForegroundColor Red; exit 1 }

Clear-Host
Write-Host "╔══════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║   Legal Aid Georgia — Backend Setup v2.0     ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════════╝" -ForegroundColor Cyan

# ── 1. Prerequisites ───────────────────────────────────────────────────────────
Write-Step "[ 1 / 9 ] Checking prerequisites..."

try {
    $phpVer = php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;"
    Write-Ok "PHP $phpVer found"

    $phpMajor = [int]($phpVer.Split('.')[0])
    $phpMinor = [int]($phpVer.Split('.')[1])
    if ($phpMajor -lt 8 -or ($phpMajor -eq 8 -and $phpMinor -lt 2)) {
        Write-Fail "PHP 8.2+ required. Current: $phpVer"
    }
} catch {
    Write-Fail "PHP not found in PATH. Install PHP 8.2+ from https://www.php.net/downloads"
}

try {
    composer --version > $null 2>&1
    Write-Ok "Composer found"
} catch {
    Write-Fail "Composer not found. Install from https://getcomposer.org"
}

# Check required PHP extensions
$requiredExt = @("pdo_mysql", "gd", "mbstring", "xml", "openssl", "fileinfo", "tokenizer", "json")
foreach ($ext in $requiredExt) {
    $result = php -r "echo extension_loaded('$ext') ? 'ok' : 'missing';"
    if ($result -eq 'missing') {
        Write-Warn "PHP extension missing: $ext — some features may not work"
    }
}

# ── 2. Composer install ────────────────────────────────────────────────────────
Write-Step "[ 2 / 9 ] Installing Composer dependencies..."
composer install --no-interaction --no-progress --optimize-autoloader
if ($LASTEXITCODE -ne 0) { Write-Fail "composer install failed. Check error above." }
Write-Ok "Dependencies installed"

# ── 3. .env setup ─────────────────────────────────────────────────────────────
Write-Step "[ 3 / 9 ] Configuring environment..."

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Ok ".env created from .env.example"
} else {
    Write-Warn ".env already exists — DB settings will be updated but APP_KEY preserved."
}

# Update DB & URL settings in .env
$envContent = Get-Content ".env" -Raw
$envContent = $envContent -replace 'DB_DATABASE=.*',  "DB_DATABASE=$DBName"
$envContent = $envContent -replace 'DB_HOST=.*',      "DB_HOST=$DBHost"
$envContent = $envContent -replace 'DB_PORT=.*',      "DB_PORT=$DBPort"
$envContent = $envContent -replace 'DB_USERNAME=.*',  "DB_USERNAME=$DBUser"
$envContent = $envContent -replace 'DB_PASSWORD=.*',  "DB_PASSWORD=$DBPassword"
$envContent = $envContent -replace 'APP_URL=.*',       "APP_URL=$AppURL"
$envContent | Set-Content ".env" -Encoding UTF8
Write-Ok "Environment configured (DB: $DBName @ $DBHost)"

# ── 4. App key ────────────────────────────────────────────────────────────────
Write-Step "[ 4 / 9 ] Generating application key..."
$existingKey = (Get-Content ".env" -Raw) -match 'APP_KEY=base64:'
if (-not $existingKey) {
    php artisan key:generate --force
    Write-Ok "Application key generated"
} else {
    Write-Ok "Application key already set — skipping"
}

# ── 5. Storage directories ────────────────────────────────────────────────────
Write-Step "[ 5 / 9 ] Creating storage structure..."

$storagePaths = @(
    "storage/app/private",
    "storage/app/public",
    "storage/app/public/images",
    "storage/framework/cache/data",
    "storage/framework/sessions",
    "storage/framework/views",
    "storage/logs"
)
foreach ($p in $storagePaths) {
    if (-not (Test-Path $p)) {
        New-Item -ItemType Directory -Path $p -Force | Out-Null
    }
}

# Create .gitkeep files
foreach ($p in $storagePaths) {
    $keepFile = Join-Path $p ".gitkeep"
    if (-not (Test-Path $keepFile)) {
        New-Item -ItemType File -Path $keepFile -Force | Out-Null
    }
}

php artisan storage:link --force 2>&1 | Out-Null
Write-Ok "Storage directories created and symlinked"

# ── 6. Database ────────────────────────────────────────────────────────────────
Write-Step "[ 6 / 9 ] Running database migrations..."

# Try creating DB if not exists
$createDB = "CREATE DATABASE IF NOT EXISTS \`$DBName\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
try {
    if ($DBPassword -eq "") {
        mysql -u $DBUser -h $DBHost -P $DBPort -e $createDB 2>&1 | Out-Null
    } else {
        mysql -u $DBUser -p$DBPassword -h $DBHost -P $DBPort -e $createDB 2>&1 | Out-Null
    }
    Write-Ok "Database '$DBName' ensured"
} catch {
    Write-Warn "Could not auto-create database (create it manually if needed): $DBName"
}

php artisan migrate --force
if ($LASTEXITCODE -ne 0) { Write-Fail "Migration failed. Check DB credentials and that MySQL is running." }
Write-Ok "Migrations complete"

# ── 7. Extract seed data ───────────────────────────────────────────────────────
Write-Step "[ 7 / 9 ] Extracting seed data from frontend..."

$frontendPath = "..\legalaid2"
$seedDataPath = "$frontendPath\public\seed-data"

if (Test-Path "$frontendPath\scripts\extract-seed-data.mjs") {
    Push-Location $frontendPath
    try {
        node scripts/extract-seed-data.mjs
        if ($LASTEXITCODE -eq 0) {
            Write-Ok "Seed data extracted (services, legal questions, documents)"
        } else {
            Write-Warn "Seed data extraction had issues — some data may not be seeded"
        }
    } finally {
        Pop-Location
    }
} else {
    Write-Warn "Frontend not found at $frontendPath — some seeders will be skipped gracefully"
}

# ── 8. Seed database ───────────────────────────────────────────────────────────
Write-Step "[ 8 / 9 ] Seeding database..."

if (-not $SkipSeed) {
    php artisan db:seed --force
    if ($LASTEXITCODE -ne 0) { Write-Fail "Database seeding failed." }
    Write-Ok "Database seeded with all content"
} else {
    Write-Warn "Seeding skipped (-SkipSeed flag)"
}

# ── 9. Optimize ────────────────────────────────────────────────────────────────
Write-Step "[ 9 / 9 ] Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:upgrade 2>&1 | Out-Null
Write-Ok "Application optimized"

# ── Done ───────────────────────────────────────────────────────────────────────
Write-Host ""
Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║                    ✅ SETUP COMPLETE!                        ║" -ForegroundColor Green
Write-Host "╠══════════════════════════════════════════════════════════════╣" -ForegroundColor Green
Write-Host "║  Start server:  php artisan serve --port=8000                ║" -ForegroundColor White
Write-Host "║  Admin panel:   $AppURL/admin                   " -ForegroundColor White
Write-Host "║  Email:         admin@legalaid.ge                            ║" -ForegroundColor White
Write-Host "║  Password:      LegalAid@2026!                               ║" -ForegroundColor White
Write-Host "╠══════════════════════════════════════════════════════════════╣" -ForegroundColor Yellow
Write-Host "║  ⚠  CHANGE THE PASSWORD BEFORE GOING LIVE!                  ║" -ForegroundColor Yellow
Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""


Write-Host "`n🔧 Legal Aid Backend Setup" -ForegroundColor Cyan
Write-Host "================================`n"

# 1. Check prerequisites
Write-Host "Checking prerequisites..." -ForegroundColor Yellow

$phpVersion = php -r "echo PHP_VERSION;"
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ PHP not found. Install PHP 8.2+ and add it to PATH." -ForegroundColor Red
    exit 1
}
Write-Host "  ✅ PHP $phpVersion"

$composerVersion = composer --version 2>&1 | Select-String -Pattern "Composer version"
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Composer not found. Install from https://getcomposer.org" -ForegroundColor Red
    exit 1
}
Write-Host "  ✅ Composer found"

# 2. Install dependencies
Write-Host "`nInstalling Composer dependencies..." -ForegroundColor Yellow
composer install --no-interaction --optimize-autoloader
if ($LASTEXITCODE -ne 0) { Write-Host "❌ composer install failed" -ForegroundColor Red; exit 1 }
Write-Host "  ✅ Dependencies installed"

# 3. Create .env
Write-Host "`nSetting up .env..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "  ✅ .env created from .env.example"
} else {
    Write-Host "  ℹ  .env already exists, skipping."
}

# Update DB settings in .env
(Get-Content ".env") | ForEach-Object {
    $_ -replace "DB_DATABASE=.*", "DB_DATABASE=$DBName" `
       -replace "DB_HOST=.*",     "DB_HOST=$DBHost" `
       -replace "DB_USERNAME=.*", "DB_USERNAME=$DBUser" `
       -replace "DB_PASSWORD=.*", "DB_PASSWORD=$DBPassword"
} | Set-Content ".env"

# 4. Generate app key
Write-Host "`nGenerating app key..." -ForegroundColor Yellow
php artisan key:generate
Write-Host "  ✅ App key generated"

# 5. Storage link
Write-Host "`nCreating storage symlink..." -ForegroundColor Yellow
php artisan storage:link
Write-Host "  ✅ Storage linked"

# 6. Run migrations
Write-Host "`nRunning database migrations..." -ForegroundColor Yellow
php artisan migrate --force
if ($LASTEXITCODE -ne 0) { Write-Host "❌ Migration failed. Check DB credentials." -ForegroundColor Red; exit 1 }
Write-Host "  ✅ Migrations complete"

# 7. Seed database
Write-Host "`nSeeding database..." -ForegroundColor Yellow
php artisan db:seed --force
Write-Host "  ✅ Database seeded"

# 8. Optimize
Write-Host "`nOptimizing..." -ForegroundColor Yellow
php artisan optimize
php artisan filament:upgrade
Write-Host "  ✅ Optimized"

Write-Host "`n" + ("=" * 50) -ForegroundColor Green
Write-Host "✅ Setup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Start the development server:" -ForegroundColor Cyan
Write-Host "  php artisan serve --port=8000"
Write-Host ""
Write-Host "Admin panel:" -ForegroundColor Cyan
Write-Host "  http://localhost:8000/admin"
Write-Host "  Email:    admin@legalaid.ge"
Write-Host "  Password: LegalAid@2026!"
Write-Host ""
Write-Host "API base URL:" -ForegroundColor Cyan
Write-Host "  http://localhost:8000/api/v1/"
Write-Host ""
Write-Host "⚠️  Change the default admin password after first login!" -ForegroundColor Red
Write-Host ("=" * 50) + "`n" -ForegroundColor Green
