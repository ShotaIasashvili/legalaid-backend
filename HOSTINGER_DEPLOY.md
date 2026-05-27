# Hostinger Deployment

This deployment flow now serves the public React site, the Laravel API, and the Filament admin panel from one `public_html` on one domain.

Default production domain:

- `https://www.new.legalaid.ge`

## Build The Package

Build the frontend first so `legalaid2/dist` contains the current SPA build:

```powershell
Set-Location ..\legalaid2
npm run build

Set-Location ..\backend
pwsh -File .\scripts\build-hostinger-package.ps1
```

PowerShell 5.1 also works:

```powershell
Set-Location ..\legalaid2
npm run build

Set-Location ..\backend
.\scripts\build-hostinger-package.ps1
```

The script defaults both the public site and Laravel app to `https://www.new.legalaid.ge`.

If `backend/.env` contains `DEPLOY_DB_HOST`, `DEPLOY_DB_PORT`, `DEPLOY_DB_DATABASE`, `DEPLOY_DB_USERNAME`, and `DEPLOY_DB_PASSWORD`, those values are copied into the packaged installer so `/install` opens with the production database already filled in.

## Package Layout

```text
public_html/
  .htaccess
  index.html
  index.php
  assets/
  manifest.webmanifest
  sw.js
  css/
  img/
  js/
  news-assets/
  news-data/
  seed-data/
  consultation-data/
  UPLOAD-FIRST.txt
  legalaid2/
    public/
      seed-data/
      news-data/
      news-assets/
      legal-acts/
  laravel/
    app/
    bootstrap/
    config/
    database/
    public/
    resources/
    routes/
    storage/
    vendor/
    .env
    artisan
```

## How Routing Works

- Public site routes are served by the React build in `public_html/index.html`.
- `/api/*`, `/admin/*`, `/livewire/*`, `/sanctum/*`, `/storage/*`, `/legacy-post-assets/*`, and `/up` are sent to Laravel through `public_html/index.php`.
- Static frontend files such as `assets/`, `manifest.webmanifest`, `sw.js`, `news-data/`, and `consultation-data/` are served directly from `public_html`.
- The extra `public_html/legalaid2/public` folder is kept only for seeders and legacy data imports.

## Why This Layout

- Hostinger shared hosting expects the live site under `public_html`.
- The React public site and Laravel backend stay on the same domain with no separate API host or subdomain.
- Laravel itself still lives in a hidden `laravel/` subfolder, which keeps the app code out of the public web root.
- `/storage/...` requests still fall back to Laravel, so no public symlink is required in `public_html`.

## After Uploading To Hostinger

1. Extract the ZIP directly into `public_html`.
2. Ensure `laravel/storage` and `laravel/bootstrap/cache` are writable.
3. Open the main domain or `/install`.
4. Confirm the prefilled database settings and run the installer.
5. After installation finishes, sign in at `/admin` and change the default password immediately.

## Runtime Defaults In The Package

- `APP_URL=https://www.new.legalaid.ge`
- `FRONTEND_URL=https://www.new.legalaid.ge`
- `FRONTEND_PROD_URL=https://www.new.legalaid.ge`
- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`

These defaults keep the deployment compatible with shared hosting without requiring database-backed sessions, cache tables, or a queue worker.

The package also keeps `APP_KEY` in place so the installer can boot normally on first load, then writes the production database settings from the installer form before running migrations and seeds.

## Notes

- In production, the React app should not set `VITE_API_URL`; it now falls back to the current origin and calls `/api/v1/...` on the same host.
- The service worker is configured to leave `/admin`, `/api`, `/livewire`, `/sanctum`, `/storage`, and `/legacy-post-assets` under Laravel control instead of treating them like SPA routes.

## Troubleshooting

If `/admin` loads but Login fails on `/livewire/update`, the most common causes are:

- `DB_HOST` is wrong for the Hostinger MySQL server.
- the installer was not completed, so the `users` table does not exist yet.
- the server is still using an older `.env` with `CACHE_STORE=database` or `SESSION_DRIVER=database`.