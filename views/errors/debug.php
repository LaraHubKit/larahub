<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (int) $code ?> Error - <?= htmlspecialchars($appName) ?></title>
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
            --color-line: #fbbf24;
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
        .container { max-width: 900px; margin: 0 auto; }
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
        h1 {
            font-size: 1.5rem;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
            word-break: break-word;
        }
        .meta {
            color: var(--color-text-muted);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        .meta a { color: var(--color-code); text-decoration: none; }
        .meta a:hover { text-decoration: underline; }
        .block {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 0.5rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            overflow-x: auto;
        }
        .block-title {
            color: var(--color-text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .block pre {
            white-space: pre-wrap;
            word-break: break-all;
            font-size: 0.85rem;
            font-family: 'JetBrains Mono', monospace;
        }
        .file-path { color: var(--color-code); }
        .line-num { color: var(--color-line); }
        .trace-list { list-style: none; font-size: 0.85rem; }
        .trace-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--glass-border);
        }
        .trace-list li:last-child { border-bottom: none; }
        .trace-file { color: var(--color-code); }
        .trace-line { color: var(--color-line); }
    </style>
</head>
<body>
    <div class="container">
        <span class="badge"><?= (int) $code ?> Error · Debug Mode</span>
        <h1><?= htmlspecialchars($message) ?></h1>
        <p class="meta">
            <strong><?= htmlspecialchars($exception) ?></strong> in
            <span class="file-path"><?= htmlspecialchars($file) ?></span> on line <span class="line-num"><?= (int) $line ?></span>
        </p>

        <div class="block">
            <div class="block-title">Stack trace</div>
            <ol class="trace-list">
                <?php foreach ($trace as $i => $t): ?>
                <li>
                    #<?= $i ?> <?= isset($t['class']) ? htmlspecialchars($t['class'] . $t['type'] . $t['function']) : htmlspecialchars($t['function']) ?>()
                    <?php if (!empty($t['file'])): ?>
                    &rarr; <span class="trace-file"><?= htmlspecialchars($t['file']) ?></span>:<span class="trace-line"><?= (int) ($t['line'] ?? '?') ?></span>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</body>
</html>
