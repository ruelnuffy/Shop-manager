<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>cPanel & Staging Deployment Suite &bull; Jessi's ClassiQ Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Poppins:wght@500;600;700&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0d0b10;
            --surface: #15131a;
            --surface-hover: #1c1924;
            --surface-card: #191621;
            --border: rgba(255, 255, 255, 0.08);
            --border-highlight: rgba(244, 63, 94, 0.35);
            --pink: #f43f5e;
            --pink-hover: #e11d48;
            --pink-glow: rgba(244, 63, 94, 0.2);
            --emerald: #10b981;
            --amber: #f59e0b;
            --rose: #ef4444;
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.65);
            --text-muted: rgba(255, 255, 255, 0.4);
            --font-display: 'Plus Jakarta Sans', system-ui, sans-serif;
            --font-body: 'Plus Jakarta Sans', system-ui, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg);
            color: var(--text-primary);
            font-family: var(--font-body);
            line-height: 1.5;
            min-height: 100vh;
            padding: 32px 16px 64px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Top Header */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 28px;
        }

        .brand-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 9999px;
            background: rgba(244, 63, 94, 0.12);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #fb7185;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--pink);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        .header-title {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 9999px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text-secondary);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-link:hover {
            background: var(--surface-hover);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Diagnostics Bar */
        .diag-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .diag-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .diag-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .diag-value {
            font-family: var(--font-mono);
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            word-break: break-all;
        }

        .status-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-success {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .status-warning {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        /* Hero Action Banner */
        .hero-banner {
            background: linear-gradient(135deg, #1b131f 0%, #17141f 50%, #121017 100%);
            border: 1px solid var(--border-highlight);
            border-radius: 24px;
            padding: 24px 28px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
        }

        .hero-info h2 {
            font-family: var(--font-display);
            font-size: 19px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .hero-info p {
            font-size: 12px;
            color: var(--text-secondary);
            max-width: 520px;
        }

        .btn-hero {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 9999px;
            background: var(--pink);
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 20px var(--pink-glow);
            transition: all 0.2s;
        }

        .btn-hero:hover {
            background: var(--pink-hover);
            transform: translateY(-1px);
        }

        /* Action Cards Grid */
        .section-title {
            font-family: var(--font-display);
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-secondary);
            margin-bottom: 14px;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 14px;
            margin-bottom: 32px;
        }

        .action-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 16px;
            transition: all 0.15s;
        }

        .action-card:hover {
            background: var(--surface-hover);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .action-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }

        .action-title {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }

        .action-desc {
            font-size: 11px;
            color: var(--text-secondary);
            line-height: 1.45;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 9px 14px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-action:hover {
            background: #fff;
            color: #000;
            border-color: #fff;
        }

        /* Results / Terminal Box */
        .terminal-box {
            background: #09080c;
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 32px;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.4);
        }

        .terminal-header {
            background: #121017;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
        }

        .terminal-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .terminal-dots {
            display: flex;
            gap: 6px;
        }

        .terminal-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .terminal-content {
            padding: 20px;
            font-family: var(--font-mono);
            font-size: 12px;
            line-height: 1.6;
            color: #d1d5db;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Instructions & Automation */
        .instructions-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 24px;
        }

        .instructions-card h3 {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .instructions-card p {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .code-snippet {
            background: #09080c;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 16px;
            font-family: var(--font-mono);
            font-size: 11px;
            color: #fb7185;
            overflow-x: auto;
            margin-bottom: 12px;
            display: block;
        }

        @media (max-width: 640px) {
            .hero-banner {
                flex-direction: column;
                align-items: flex-start;
            }
            .btn-hero {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Top App Bar --}}
        <header class="header">
            <div class="brand-group">
                <span class="badge-pill">
                    <span class="badge-dot"></span>
                    <span>Staging &amp; cPanel Suite</span>
                </span>
                <h1 class="header-title">Jessi's ClassiQ Store</h1>
            </div>

            <nav class="header-nav">
                <a href="{{ route('home') }}" class="btn-link" target="_blank">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Storefront &rarr;</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn-link" target="_blank">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    <span>Admin Atelier &rarr;</span>
                </a>
            </nav>
        </header>

        {{-- System Diagnostics Overview --}}
        <div class="diag-grid">
            {{-- Diagnostic 1: Database Connection --}}
            <div class="diag-card">
                <div class="diag-label">
                    <span>Database Engine</span>
                    @if($diagnostics['database_connected'])
                        <span class="status-tag status-success">Connected</span>
                    @else
                        <span class="status-tag status-danger">Failed</span>
                    @endif
                </div>
                <div class="diag-value">
                    @if($diagnostics['database_connected'])
                        {{ $diagnostics['database_name'] }}
                    @else
                        <span style="color: #f87171; font-size: 11px;">{{ $diagnostics['database_error'] }}</span>
                    @endif
                </div>
            </div>

            {{-- Diagnostic 2: PHP & Framework --}}
            <div class="diag-card">
                <div class="diag-label">
                    <span>Environment Runtime</span>
                    <span class="status-tag status-success">Ready</span>
                </div>
                <div class="diag-value">
                    PHP {{ $diagnostics['php_version'] }} &bull; L{{ $diagnostics['laravel_version'] }}
                </div>
            </div>

            {{-- Diagnostic 3: Storage Symlink --}}
            <div class="diag-card">
                <div class="diag-label">
                    <span>Storage Symlink</span>
                    @if($diagnostics['storage_link_active'])
                        <span class="status-tag status-success">Linked</span>
                    @else
                        <span class="status-tag status-warning">Not Linked</span>
                    @endif
                </div>
                <div class="diag-value" style="font-size: 12px;">
                    {{ $diagnostics['storage_link_active'] ? 'public/storage active' : 'Click "Link Storage" below' }}
                </div>
            </div>

            {{-- Diagnostic 4: Vite Production Manifest --}}
            <div class="diag-card">
                <div class="diag-label">
                    <span>Assets Compiled</span>
                    @if($diagnostics['public_build_manifest'])
                        <span class="status-tag status-success">Built</span>
                    @else
                        <span class="status-tag status-warning">Pending</span>
                    @endif
                </div>
                <div class="diag-value" style="font-size: 12px;">
                    {{ $diagnostics['public_build_manifest'] ? 'Vite Production Manifest' : 'Run npm run build' }}
                </div>
            </div>
        </div>

        {{-- 1-Click Master Deployment Banner --}}
        <div class="hero-banner">
            <div class="hero-info">
                <h2>1-Click Complete Staging Setup</h2>
                <p>
                    Executes all pending database migrations (<code>migrate --force</code>), establishes public image upload symlinks (<code>storage:link</code>), and caches routes and configuration for high-speed performance.
                </p>
            </div>
            <a href="{{ route('cpanel.deploy', ['key' => $key, 'action' => 'all']) }}" class="btn-hero">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Run Full Setup Now</span>
            </a>
        </div>

        {{-- Terminal / Command Execution Output --}}
        @if(!empty($results))
            <div class="terminal-box">
                <div class="terminal-header">
                    <div class="terminal-title">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>artisan: {{ $results['action'] }} &bull; {{ $results['timestamp'] }}</span>
                    </div>
                    <div class="terminal-dots">
                        <span class="terminal-dot" style="background: #ef4444;"></span>
                        <span class="terminal-dot" style="background: #f59e0b;"></span>
                        <span class="terminal-dot" style="background: #10b981;"></span>
                    </div>
                </div>
                <div class="terminal-content">@if(!empty($results['output'])){{ $results['output'] }}@else
Command executed successfully with zero error output.@endif</div>
            </div>
        @endif

        {{-- Modular Action Cards --}}
        <h2 class="section-title">Individual Staging Operations</h2>
        <div class="action-grid">
            {{-- Action 1: Migrations --}}
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    </div>
                    <div>
                        <div class="action-title">Run Migrations</div>
                        <div class="action-desc">Applies all database schema tables, indexes, and column updates without dropping existing data.</div>
                    </div>
                </div>
                <a href="{{ route('cpanel.deploy', ['key' => $key, 'action' => 'migrate']) }}" class="btn-action">
                    Run `migrate --force`
                </a>
            </div>

            {{-- Action 2: Seeders --}}
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <div class="action-title">Seed Boutique Data</div>
                        <div class="action-desc">Populates default luxury gowns, leather bags, admin accounts, and store location settings.</div>
                    </div>
                </div>
                <a href="{{ route('cpanel.deploy', ['key' => $key, 'action' => 'seed']) }}" class="btn-action">
                    Run `db:seed --force`
                </a>
            </div>

            {{-- Action 3: Migrate & Seed Together --}}
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="action-title">Migrate &amp; Seed</div>
                        <div class="action-desc">Sequential execution of schema migrations followed by database seeding in a single click.</div>
                    </div>
                </div>
                <a href="{{ route('cpanel.deploy', ['key' => $key, 'action' => 'migrate_seed']) }}" class="btn-action">
                    Migrate + Seed
                </a>
            </div>

            {{-- Action 4: Storage Link --}}
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <div>
                        <div class="action-title">Link Public Storage</div>
                        <div class="action-desc">Creates or repairs the <code>public/storage</code> symlink so product photos and logos display properly.</div>
                    </div>
                </div>
                <a href="{{ route('cpanel.deploy', ['key' => $key, 'action' => 'storage_link']) }}" class="btn-action">
                    Run `storage:link`
                </a>
            </div>

            {{-- Action 5: Optimize & Cache --}}
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <div class="action-title">Optimize &amp; Cache</div>
                        <div class="action-desc">Pre-compiles routes, configuration files, and blade views for maximum page-load responsiveness.</div>
                    </div>
                </div>
                <a href="{{ route('cpanel.deploy', ['key' => $key, 'action' => 'optimize']) }}" class="btn-action">
                    Run `optimize`
                </a>
            </div>

            {{-- Action 6: Clear Caches --}}
            <div class="action-card">
                <div class="action-header">
                    <div class="action-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <div class="action-title">Flush All Caches</div>
                        <div class="action-desc">Clears cached configuration, compiled templates, and stale sessions during active debugging.</div>
                    </div>
                </div>
                <a href="{{ route('cpanel.deploy', ['key' => $key, 'action' => 'clear_cache']) }}" class="btn-action">
                    Run `optimize:clear`
                </a>
            </div>
        </div>

        {{-- Direct URL Automation / Cron Usage --}}
        <div class="instructions-card">
            <h3>Direct URL &amp; cPanel Automation</h3>
            <p>
                You can trigger any operation automatically from cPanel Cron Jobs, terminal curl commands, or browser bookmarks without visiting this page interactively:
            </p>

            <span class="code-snippet">
                {{ url('/cpanel-deploy') }}?key={{ $key }}&amp;action=migrate&amp;format=json
            </span>

            <p style="margin-top: 14px; font-size: 11px; color: var(--text-muted);">
                <strong>Permissions Note:</strong> Ensure that cPanel has write permissions on the following folders (typically chmod <code>775</code> or <code>755</code>):<br>
                <code>storage/</code>, <code>storage/logs/</code>, <code>storage/framework/</code>, and <code>bootstrap/cache/</code>.
            </p>
        </div>
    </div>
</body>
</html>
