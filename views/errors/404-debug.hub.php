<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - <?= htmlspecialchars($appName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #e53935;
            --color-bg: #000000;
            --color-text: #e2e8f0;
            --color-text-muted: #94a3b8;
            --color-code: #60a5fa;
            --glass-bg: rgba(255, 255, 255, 0.04);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Nunito', system-ui, sans-serif;
            background: #000;
            min-height: 100vh;
            color: var(--color-text);
            padding: 2rem;
            line-height: 1.6;
        }
        .container { max-width: 560px; margin: 0 auto; }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: var(--color-primary);
            color: #fff;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }
        h1 { font-size: 1.5rem; color: var(--color-primary); margin-bottom: 1rem; }
        .block {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 0.5rem;
            padding: 1rem;
            font-size: 0.9rem;
        }
        .block dt { color: var(--color-text-muted); font-size: 0.75rem; margin-top: 0.5rem; }
        .block dd { margin-left: 0; font-family: 'JetBrains Mono', monospace; color: var(--color-code); }
    </style>
</head>
<body>
    <div class="container">
        <span class="badge">404 · Debug Mode</span>
        <h1>Page not found</h1>
        <div class="block">
            <dl>
                <dt>Request URI</dt>
                <dd><?= htmlspecialchars($uri) ?></dd>
                <dt>Method</dt>
                <dd><?= htmlspecialchars($method) ?></dd>
            </dl>
        </div>
    </div>
</body>
</html>
