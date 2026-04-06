# Legal Aid — Laravel Backend

Full-featured **PHP Laravel 11** backend with **Filament v3** admin panel for the Legal Aid Service website.

---

## ✅ Features

### Admin Panel (`/admin`)
| Section | Features |
|---------|----------|
| **News Dashboard** | Full CRUD • Rich text editor • Image upload with auto-resize/compress/WebP • Bulk publish • Draft/Scheduled states • Featured posts |
| **Services** | Full content editor • Requirements, steps, download links as repeaters • Sort order drag & drop |
| **FAQ** | Georgian & bilingual Q&A • Categories • Sort order |
| **Legal Questions** | Same as FAQ with separate section |
| **Staff & Directors** | Photo upload (auto-resized) • Roles: director / former director / honorary / council • Bio • Career/Education |
| **Offices** | Map coordinates • Region/City filter • Services list |
| **Documents / PDFs** | Upload PDFs up to 50MB • Auto badge • Download counter • Types: legal act / registry / council decision / public info / form |
| **Journals** | Cover image processing • PDF upload • Download counter |
| **Vacancies** | Rich editor • Deadline • Status badges • Open counter in nav |
| **Videos** | YouTube URL → ID auto-extraction • Thumbnail auto-set |
| **Projects** | Image processing • Partner / Donor / Status |
| **Page Contents** | Key-value blocks per page • Types: text / HTML / image / JSON / boolean |
| **Settings** | Grouped site settings: general / contact / social / SEO / footer |
| **Stats** | Homepage stats blocks |

### Image Processing
Every image upload is automatically processed into **5 versions**:

| Variant | Size | Purpose |
|---------|------|---------|
| `original` | max 1920px wide | Full resolution |
| `thumbnail` | 400 × 280 | News card thumbnail |
| `popup` | 800 × 500 | Modal / popup preview |
| `single` | 1200 × 750 | Single article hero |
| `og` | 1200 × 630 | Open Graph / social share |

Plus **WebP versions** of `original` and `thumbnail` for performance.

### REST API
Base URL: `GET /api/v1/`

```
GET  /posts               — paginated news list
GET  /posts/latest        — last N posts
GET  /posts/{slug}        — single post
GET  /services            — services list
GET  /services/{slug}     — single service
GET  /faqs                — FAQ items
GET  /legal-questions     — legal Q&A
GET  /documents           — documents/PDFs
GET  /documents/{id}/download
GET  /staff               — staff list (?type=director)
GET  /staff/{id}          — staff detail
GET  /offices             — offices (?type=bureau&region=...)
GET  /journals            — journals list
GET  /journals/{id}/download
GET  /vacancies           — vacancies
GET  /vacancies/{slug}    — single vacancy
GET  /videos              — videos
GET  /projects            — projects
GET  /content/{page}      — page content blocks
GET  /settings            — site settings
GET  /stats               — stats
GET  /search?q=...        — global search
```

---

## ⚡ Quick Setup (Windows)

### Prerequisites
- **PHP 8.2+** — https://windows.php.net/download
- **Composer** — https://getcomposer.org
- **MySQL 8.0+** or **MariaDB 10.6+**

### 1. Install & Run

```powershell
cd backend

.\setup.ps1 -DBName "legalaid" -DBUser "root" -DBPassword "yourpassword"
```

This will:
- Install all Composer dependencies
- Create `.env` from `.env.example`
- Generate app key
- Run migrations (creates all tables)
- Seed the database + import posts from `posts.json`
- Create storage symlink

### 2. Start server

```powershell
php artisan serve --port=8000
```

### 3. Login to Admin

Open → http://localhost:8000/admin

```
Email:    admin@legalaid.ge
Password: LegalAid@2026!
```

> ⚠️ **Change the password immediately after first login!**

---

## 🔗 Connecting the React Frontend

In your `legalaid2` project, update the API base:

```typescript
// src/lib/api.ts
export const API_BASE = 'http://localhost:8000/api/v1';

// Example: fetch latest news
const posts = await fetch(`${API_BASE}/posts?per_page=5&category=სიახლეები`).then(r => r.json());
```

For production, set `FRONTEND_PROD_URL` in `.env` and configure your web server.

---

## 🏗️ Project Structure

```
backend/
├── app/
│   ├── Filament/
│   │   ├── Resources/          ← All admin CRUD panels
│   │   │   ├── PostResource.php
│   │   │   ├── ServiceResource.php
│   │   │   ├── StaffResource.php
│   │   │   ├── DocumentResource.php
│   │   │   ├── FaqResource.php
│   │   │   ├── OfficeResource.php
│   │   │   ├── JournalResource.php
│   │   │   ├── VacancyResource.php
│   │   │   ├── VideoResource.php
│   │   │   ├── PageContentResource.php
│   │   │   └── SettingResource.php
│   │   ├── Widgets/
│   │   │   └── StatsOverview.php  ← Dashboard stats widget
│   │   └── ...
│   ├── Http/Controllers/Api/      ← JSON API endpoints
│   │   ├── PostController.php
│   │   ├── ServiceController.php
│   │   ├── FaqController.php
│   │   ├── DocumentController.php
│   │   ├── StaffController.php
│   │   ├── OfficeController.php
│   │   ├── MiscController.php     ← journals, vacancies, videos, projects
│   │   ├── ContentController.php  ← page content, settings, stats, search
│   │   └── MediaController.php    ← image upload API
│   ├── Models/                    ← Eloquent models (all entities)
│   ├── Services/
│   │   └── ImageService.php       ← Image resize + WebP + compression
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── Filament/AdminPanelProvider.php
├── database/
│   ├── migrations/               ← All table schemas
│   └── seeders/
│       ├── DatabaseSeeder.php    ← Creates admin + calls sub-seeders
│       ├── PostSeeder.php        ← Imports from posts.json
│       └── SettingsSeeder.php    ← Default site settings
├── routes/
│   ├── api.php                   ← All API routes
│   └── web.php
├── config/
│   ├── cors.php                  ← Configured for React :5173
│   └── image_processing.php     ← Resize dimensions, quality, WebP
├── .env.example
├── composer.json
└── setup.ps1                    ← Windows one-click setup
```

---

## ⚙️ Environment Variables

Key settings in `.env`:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=legalaid
DB_USERNAME=root
DB_PASSWORD=

# React frontend URLs (for CORS)
FRONTEND_URL=http://localhost:5173
FRONTEND_PROD_URL=https://legalaid.ge

# Image Processing
IMAGE_THUMBNAIL_WIDTH=400
IMAGE_THUMBNAIL_HEIGHT=280
IMAGE_POPUP_WIDTH=800
IMAGE_POPUP_HEIGHT=500
IMAGE_SINGLE_WIDTH=1200
IMAGE_SINGLE_HEIGHT=750
IMAGE_QUALITY=82

# Image driver: 'gd' (default, built into PHP) or 'imagick'
IMAGE_DRIVER=gd
```

---

## 🔒 Security Notes

- All admin routes protected by Filament auth
- API is **read-only** for public, write operations require Sanctum token
- File upload MIME type validation enforced
- SQL injection protected via Eloquent ORM
- XSS: Rich editor content is stored as HTML (sanitize on frontend render)
- CORS restricted to `FRONTEND_URL` and `FRONTEND_PROD_URL` only

---

## 🛠️ Useful Commands

```powershell
# Create a new admin user
php artisan make:filament-user

# Re-import posts from JSON
php artisan db:seed --class=PostSeeder

# Clear all caches
php artisan optimize:clear

# Run migrations fresh (⚠️ drops all data)
php artisan migrate:fresh --seed
```
