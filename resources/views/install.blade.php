<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Legal Aid Installer</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #efe8dc;
            --panel: rgba(255, 252, 247, 0.92);
            --line: rgba(94, 58, 41, 0.14);
            --text: #231815;
            --muted: #6b5a53;
            --brand: #8b2635;
            --brand-dark: #671a26;
            --ok: #1e7f5b;
            --error: #b83232;
            --shadow: 0 28px 60px rgba(44, 26, 20, 0.16);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Georgia, "Times New Roman", serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(139, 38, 53, 0.18), transparent 34%),
                linear-gradient(135deg, #f7f1e6 0%, #efe8dc 52%, #e5ddd2 100%);
        }

        .shell {
            width: min(1080px, calc(100% - 32px));
            margin: 32px auto;
            display: grid;
            gap: 24px;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 28px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }

        .hero {
            padding: 32px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--brand);
            background: rgba(139, 38, 53, 0.08);
        }

        h1 {
            margin: 18px 0 12px;
            font-size: clamp(34px, 5vw, 58px);
            line-height: 0.95;
            letter-spacing: -0.04em;
        }

        p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .stack {
            display: grid;
            gap: 16px;
            margin-top: 26px;
        }

        .card {
            padding: 22px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #4b3d38;
        }

        input {
            width: 100%;
            border: 1px solid rgba(93, 71, 61, 0.22);
            border-radius: 14px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--text);
            font-size: 15px;
        }

        input:focus {
            outline: 2px solid rgba(139, 38, 53, 0.18);
            border-color: rgba(139, 38, 53, 0.38);
        }

        button {
            border: 0;
            border-radius: 16px;
            padding: 15px 18px;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            cursor: pointer;
        }

        .notice,
        .success,
        .warning {
            padding: 16px 18px;
            border-radius: 18px;
            margin-bottom: 18px;
        }

        .notice {
            background: rgba(184, 50, 50, 0.08);
            color: var(--error);
            border: 1px solid rgba(184, 50, 50, 0.18);
        }

        .success {
            background: rgba(30, 127, 91, 0.08);
            color: var(--ok);
            border: 1px solid rgba(30, 127, 91, 0.18);
        }

        .warning {
            background: rgba(139, 38, 53, 0.06);
            color: #5e3929;
            border: 1px solid rgba(94, 57, 41, 0.16);
        }

        .meta {
            display: grid;
            gap: 14px;
            margin-top: 22px;
        }

        .meta strong {
            display: block;
            margin-bottom: 4px;
            color: var(--text);
        }

        .output {
            margin-top: 18px;
            padding: 16px;
            border-radius: 16px;
            background: #1a1514;
            color: #efe8dc;
            font: 13px/1.55 Consolas, Monaco, monospace;
            white-space: pre-wrap;
            word-break: break-word;
        }

        a {
            color: var(--brand);
            font-weight: 700;
            text-decoration: none;
        }

        ul {
            margin: 0;
            padding-left: 18px;
            color: var(--muted);
        }

        @media (max-width: 900px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="panel hero">
            <div class="eyebrow">Same-domain deployment</div>
            <h1>Legal Aid installer</h1>
            <p>
                The frontend, API, and admin panel stay on the same domain. On first run, use this screen once,
                connect the database, and the installer will write the environment, run migrations, and seed the site.
            </p>

            <div class="stack">
                <div class="panel card">
                    <strong>What this does</strong>
                    <ul>
                        <li>keeps the public site and Laravel backend on one domain</li>
                        <li>writes the database settings into the Laravel environment</li>
                        <li>runs migrations and seeds the bundled content automatically</li>
                        <li>unlocks the admin panel after setup finishes</li>
                    </ul>
                </div>

                <div class="panel card">
                    <strong>Bundled admin account</strong>
                    <ul>
                        <li>Email: admin@legalaid.ge</li>
                        <li>Password: LegalAid@2026!</li>
                        <li>Change this password immediately after installation</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="panel card">
            @if ($errors->any())
                <div class="notice">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if ($installed && $installInfo)
                <div class="success">
                    Installation completed successfully. The application is now ready.
                </div>

                <div class="meta">
                    <div>
                        <strong>Site URL</strong>
                        <a href="{{ $installInfo['app_url'] }}">{{ $installInfo['app_url'] }}</a>
                    </div>
                    <div>
                        <strong>Admin panel</strong>
                        <a href="{{ $installInfo['admin_url'] }}">{{ $installInfo['admin_url'] }}</a>
                    </div>
                    <div>
                        <strong>Admin email</strong>
                        {{ $installInfo['admin_email'] }}
                    </div>
                    <div>
                        <strong>Admin password</strong>
                        {{ $installInfo['admin_password'] }}
                    </div>
                </div>

                @if (!empty($installInfo['migrate_output']))
                    <div class="output">{{ $installInfo['migrate_output'] }}</div>
                @endif

                @if (!empty($installInfo['seed_output']))
                    <div class="output">{{ $installInfo['seed_output'] }}</div>
                @endif
            @elseif ($installed)
                <div class="success">
                    Installation is already complete. You can continue to <a href="{{ url('/admin') }}">the admin panel</a>.
                </div>
            @else
                <div class="warning">
                    Use the same domain here for the public site, API, and admin panel. Example: https://yourdomain.com
                </div>

                <form method="post" action="{{ route('install.run') }}">
                    @csrf

                    <div class="grid">
                        <div class="field full">
                            <label for="app_url">Site URL</label>
                            <input id="app_url" name="app_url" type="url" value="{{ old('app_url', $defaults['app_url']) }}" required>
                        </div>

                        <div class="field">
                            <label for="db_host">Database host</label>
                            <input id="db_host" name="db_host" type="text" value="{{ old('db_host', $defaults['db_host']) }}" required>
                        </div>

                        <div class="field">
                            <label for="db_port">Database port</label>
                            <input id="db_port" name="db_port" type="number" value="{{ old('db_port', $defaults['db_port']) }}" required>
                        </div>

                        <div class="field full">
                            <label for="db_database">Database name</label>
                            <input id="db_database" name="db_database" type="text" value="{{ old('db_database', $defaults['db_database']) }}" required>
                        </div>

                        <div class="field">
                            <label for="db_username">Database username</label>
                            <input id="db_username" name="db_username" type="text" value="{{ old('db_username', $defaults['db_username']) }}" required>
                        </div>

                        <div class="field">
                            <label for="db_password">Database password</label>
                            <input id="db_password" name="db_password" type="text" value="{{ old('db_password', $defaults['db_password']) }}">
                        </div>
                    </div>

                    <div class="stack">
                        <button type="submit">Install application</button>
                    </div>
                </form>
            @endif
        </section>
    </div>
</body>
</html>