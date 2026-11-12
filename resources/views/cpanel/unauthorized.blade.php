<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Deployment Gateway Access Restricted &bull; Jessi's ClassiQ Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Poppins:wght@500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f0d13;
            color: #f3f3f3;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            width: 100%;
            max-width: 520px;
            padding: 24px;
            box-sizing: border-box;
        }
        .card {
            background: #17151e;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            text-align: center;
        }
        .icon-box {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(225, 29, 72, 0.15);
            color: #fb7185;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .title {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-weight: 800;
            font-size: 22px;
            margin: 0 0 10px 0;
            letter-spacing: -0.02em;
        }
        .subtitle {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .input-group {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }
        .key-input {
            flex: 1;
            background: #0f0d13;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 12px 16px;
            color: #fff;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            outline: none;
            box-sizing: border-box;
        }
        .key-input:focus {
            border-color: #f43f5e;
        }
        .btn-submit {
            background: #f43f5e;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-submit:hover {
            background: #e11d48;
        }
        .info-pill {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 14px;
            text-align: left;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.5;
        }
        .info-pill code {
            font-family: 'JetBrains Mono', monospace;
            color: #fb7185;
            background: rgba(251, 113, 133, 0.1);
            padding: 2px 6px;
            border-radius: 6px;
        }
        .home-link {
            display: inline-block;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }
        .home-link:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon-box">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>

            <h1 class="title">Staging & cPanel Deployment Gateway</h1>
            <p class="subtitle">
                Access to run migrations, database seeders, and cache configuration via URL is protected by a secure key.
            </p>

            <form method="GET" action="{{ route('cpanel.deploy') }}">
                <div class="input-group">
                    <input
                        type="password"
                        name="key"
                        placeholder="Enter deployment key..."
                        value="{{ $providedKey }}"
                        required
                        class="key-input"
                        autofocus
                    >
                    <button type="submit" class="btn-submit">
                        Unlock &rarr;
                    </button>
                </div>
            </form>

            <div class="info-pill">
                <strong>Deployment Note:</strong> You can pass the key directly in the URL:
                <br>
                <code>?key=YOUR_KEY</code>
                <br><br>
                The default staging key is configured in your <code>.env</code> file under <code>CPANEL_SETUP_KEY</code>.
            </div>

            <a href="{{ route('home') }}" class="home-link">
                &larr; Return to Boutique Home
            </a>
        </div>
    </div>
</body>
</html>
