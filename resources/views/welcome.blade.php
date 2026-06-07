<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legal Aid — Backend API</title>
    <style>
        body { font-family: -apple-system, sans-serif; background: #f4f4f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 2.5rem 3rem; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); text-align: center; max-width: 400px; }
        h1 { color: #8B2635; margin: 0 0 .5rem; font-size: 1.5rem; }
        p { color: #52525b; margin: .25rem 0; font-size: .95rem; }
        a { color: #8B2635; text-decoration: none; font-weight: 600; }
        a:hover { text-decoration: underline; }
        .badge { display: inline-block; background: #fef2f2; color: #8B2635; border: 1px solid #fecaca; border-radius: 6px; padding: .25rem .75rem; font-size: .8rem; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>⚖️ Legal Aid Georgia</h1>
        <p>Backend API Server</p>
        <p style="margin-top:1rem;"><a href="/admin">→ Admin Panel</a></p>
        <p><a href="/api/v1/posts">→ API: Posts</a></p>
        <span class="badge">Laravel {{ Illuminate\Foundation\Application::VERSION }}</span>
    </div>
</body>
</html>
