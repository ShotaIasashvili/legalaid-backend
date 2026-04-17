# Hostinger Deployment

Use [scripts/build-hostinger-package.ps1](scripts/build-hostinger-package.ps1) to generate a ZIP that can be extracted directly into `public_html`.

The generated package has this structure:

```text
public_html/
  .htaccess
  index.php
  css/
  img/
  js/
  UPLOAD-FIRST.txt
  legalaid2/
    public/
      seed-data/
      news-data/
      news-assets/
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

Why this layout is used:

- The domain root keeps only the front controller and public assets.
- The Laravel app itself stays in a subfolder.
- When the sibling `legalaid2/public` folder exists locally, the package also bundles the frontend-exported seed data and legacy post assets into `public_html/legalaid2/public`.
- Requests for `/storage/...` fall back to Laravel and are served from `storage/app/public`, so shared hosting does not need a symlink in `public_html`.

Default package values:

- Backend URL: `https://powderblue-rhinoceros-805295.hostingersite.com`
- Frontend URL: `https://lightgreen-caterpillar-796045.hostingersite.com`

Build the ZIP from the repo root:

```powershell
pwsh -File .\scripts\build-hostinger-package.ps1
```

PowerShell 5.1 also works:

```powershell
.\scripts\build-hostinger-package.ps1
```

After extraction on Hostinger:

1. Edit `laravel/.env` and fill in `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and `APP_KEY`.
2. Generate `APP_KEY` if it is empty.
3. Ensure `laravel/storage` and `laravel/bootstrap/cache` are writable.
4. Run migrations if the target database is empty.
5. Run `php laravel/artisan db:seed --force` to import posts, services, legal questions, and documents when `public_html/legalaid2/public` is present.

Hostinger package defaults:

- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`

This avoids requiring database-backed cache/session tables or a queue worker on shared hosting.

Bundled legacy frontend data:

- `seed-data` gives production seeders the real frontend services, legal questions, and document metadata.
- `news-data/posts.json` gives production the real post content.
- `news-assets` preserves legacy news images and attached post assets.

If `/admin` loads but clicking Login returns a 500 error on `/livewire/update`, the most likely causes are:

- `DB_HOST` is wrong for the Hostinger MySQL server
- migrations were not run, so the `users` table does not exist
- the server is still using an older `.env` with `CACHE_STORE=database`