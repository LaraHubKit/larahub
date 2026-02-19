<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(env('APP_NAME', 'LaraHub')) ?> - Lightweight PHP Framework</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #e53935;
            --color-primary-dim: rgba(229, 57, 53, 0.25);
            --color-bg: #000000;
            --color-text: #e2e8f0;
            --color-text-muted: #94a3b8;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Nunito', system-ui, -apple-system, sans-serif;
            background: var(--color-bg);
            color: var(--color-text);
            line-height: 1.6;
            min-height: 100vh;
        }
        .header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1.5rem 2rem;
        }
        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo { font-size: 1.5rem; font-weight: 700; color: var(--color-primary); }
        .main { max-width: 1200px; margin: 0 auto; padding: 3rem 2rem; }
        .hero { text-align: center; margin-bottom: 4rem; }
        .hero h1 {
            font-size: 2.75rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 0.75rem;
        }
        .hero h1 span { color: var(--color-primary); }
        .hero p {
            font-size: 1.25rem;
            color: var(--color-text-muted);
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        .hero-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: var(--color-primary);
            color: #fff;
            border: none;
        }
        .btn-primary:hover { background: #ef5350; }
        .btn-outline {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            color: var(--color-primary);
            border: 1px solid var(--glass-border);
        }
        .btn-outline:hover { background: var(--color-primary-dim); border-color: var(--color-primary); }
        .section { margin-bottom: 3rem; }
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--color-primary);
        }
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.2s;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
        }
        .card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.18);
            transform: translateY(-2px);
        }
        .card h3 { font-size: 1.1rem; color: var(--color-primary); margin-bottom: 0.5rem; }
        .card p { color: var(--color-text-muted); font-size: 0.95rem; }
        .card-large {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
        }
        .card-large h3 { color: var(--color-primary); margin-bottom: 1rem; font-size: 1.1rem; }
        .card-large p { color: var(--color-text-muted); margin-bottom: 1rem; }
        .code-block {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 0.5rem;
            padding: 1rem 1.25rem;
            font-family: 'JetBrains Mono', Consolas, monospace;
            font-size: 0.875rem;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .code-block .cmd { color: var(--color-primary); }
        .code-block .comment { color: #6b7280; }
        .cmd-list { list-style: none; }
        .cmd-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .cmd-list li:last-child { border-bottom: none; }
        .cmd-list code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            border: 1px solid var(--glass-border);
        }
        .cmd-desc { color: var(--color-text-muted); font-size: 0.9rem; }
        .structure {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            padding: 1.5rem;
            font-family: 'JetBrains Mono', Consolas, monospace;
            font-size: 0.9rem;
            overflow-x: auto;
        }
        .structure .dir { color: var(--color-primary); }
        .structure .file { color: var(--color-text-muted); }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: var(--color-primary-dim);
            backdrop-filter: blur(8px);
            color: var(--color-primary);
            border: 1px solid rgba(229, 57, 53, 0.4);
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .req-list { list-style: none; }
        .req-list li {
            padding: 0.5rem 0;
            color: var(--color-text-muted);
            padding-left: 1.5rem;
            position: relative;
        }
        .req-list li::before { content: "→"; color: var(--color-primary); position: absolute; left: 0; }
        .env-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        .env-table th, .env-table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--glass-border); }
        .env-table th { color: var(--color-primary); font-weight: 600; }
        .env-table td { color: var(--color-text-muted); }
        .footer {
            text-align: center;
            padding: 2rem;
            color: var(--color-text-muted);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <span class="logo"><?= htmlspecialchars(env('APP_NAME', 'LaraHub')) ?></span>
            <span class="badge">PHP 8.1+</span>
        </div>
    </header>

    <main class="main">
        <section class="hero">
            <h1>Build with <span>LaraHub</span></h1>
            <p>A lightweight PHP framework for small to medium web applications. Built with simplicity and developer experience in mind.</p>
            <div class="hero-actions">
                <a href="#quick-start" class="btn btn-primary">Quick Start</a>
                <a href="#features" class="btn btn-outline">Features</a>
                <a href="#installation" class="btn btn-outline">Installation</a>
            </div>
        </section>

        <section id="quick-start" class="section">
            <h2 class="section-title">Quick Start</h2>
            <div class="card card-large">
                <p>Start the built-in development server and visit <strong>http://localhost:8000</strong></p>
                <div class="code-block"><span class="cmd">php larahub run</span></div>
            </div>
        </section>

        <section id="installation" class="section">
            <h2 class="section-title">Installation</h2>
            <div class="card card-large">
                <p>Run <code>composer install</code>, configure <code>.env</code>, then generate your application key:</p>
                <div class="code-block">
<span class="comment"># Configure environment</span><br>
cp .env.example .env<br>
<br>
<span class="comment"># Generate application key (required before running)</span><br>
<span class="cmd">php larahub generate:key</span>
                </div>
                <p>Setup runs automatically on <code>composer install</code>: copies <code>.env.example</code> to <code>.env</code> if missing, generates <code>APP_KEY</code> if not set, creates storage directories.</p>
            </div>
        </section>

        <section id="requirements" class="section">
            <h2 class="section-title">Requirements</h2>
            <div class="card card-large">
                <ul class="req-list">
                    <li><strong>PHP</strong> 8.1 or higher</li>
                    <li><strong>Composer</strong></li>
                    <li><strong>MySQL</strong> (or compatible database)</li>
                </ul>
            </div>
        </section>

        <section id="features" class="section">
            <h2 class="section-title">Features</h2>
            <div class="card-grid">
                <div class="card">
                    <h3>Eloquent ORM</h3>
                    <p>Built on Illuminate Eloquent for elegant database access. Relationships, scopes, accessors, and mutators.</p>
                </div>
                <div class="card">
                    <h3>MVC Routing</h3>
                    <p>Simple, expressive routing with controllers. Separate web and API routes in <code>routes/web.php</code> and <code>routes/api.php</code>.</p>
                </div>
                <div class="card">
                    <h3>Migrations</h3>
                    <p>Database migrations with blueprint support. Use <code>make:model -m</code> for quick setup. Blameable columns optional.</p>
                </div>
                <div class="card">
                    <h3>CLI Tools</h3>
                    <p>Artisan-style CLI: make:controller, make:model, migrate, generate:key. Fast scaffolding for new projects.</p>
                </div>
                <div class="card">
                    <h3>CSRF Protection</h3>
                    <p>Built-in CSRF tokens via <code>Security::csrfField()</code> and <code>Security::verifyCsrf()</code>. Output escaping for views.</p>
                </div>
                <div class="card">
                    <h3>Error Handling</h3>
                    <p>Debug mode with stack traces. Production-safe generic pages. 404 handling when no route matches.</p>
                </div>
                <div class="card">
                    <h3>Middleware</h3>
                    <p>Create middleware in <code>app/Middlewares/</code> with a <code>handle()</code> method. Attach to routes as needed.</p>
                </div>
                <div class="card">
                    <h3>Security</h3>
                    <p>APP_KEY stored encrypted in <code>.env</code>. Master key in <code>storage/keys/</code>. Run <code>generate:key</code> after config changes.</p>
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">CLI Commands</h2>
            <div class="card card-large">
                <ul class="cmd-list">
                    <li><code>php larahub run</code> <span class="cmd-desc">Start dev server on http://localhost:8000</span></li>
                    <li><code>php larahub migrate</code> <span class="cmd-desc">Run all database migrations</span></li>
                    <li><code>php larahub make:controller Name</code> <span class="cmd-desc">Create a new controller</span></li>
                    <li><code>php larahub make:model Name</code> <span class="cmd-desc">Create a new model</span></li>
                    <li><code>php larahub make:model Name -m</code> <span class="cmd-desc">Model with migration</span></li>
                    <li><code>php larahub make:model Name -m -b=users</code> <span class="cmd-desc">Migration + blameable columns</span></li>
                    <li><code>php larahub make:model Name -m --no-blameable</code> <span class="cmd-desc">Model + migration, no blameable</span></li>
                    <li><code>php larahub generate:key</code> <span class="cmd-desc">Generate or regenerate APP_KEY</span></li>
                </ul>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Routing</h2>
            <div class="card card-large">
                <p>Define routes in <code>routes/web.php</code> and <code>routes/api.php</code>:</p>
                <div class="code-block">
<span class="comment">// GET route</span><br>
$router->get('/about', [\App\Controllers\AboutController::class, 'index']);<br>
<br>
<span class="comment">// POST route</span><br>
$router->post('/contact', [\App\Controllers\ContactController::class, 'submit']);<br>
<br>
<span class="comment">// With middleware</span><br>
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'], ['AuthMiddleware']);
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Controllers</h2>
            <div class="card card-large">
                <p>Extend <code>Core\Controller</code> and return view output or JSON:</p>
                <div class="code-block">
return $this->view('home', ['title' => 'Welcome']);<br>
return $this->json(['status' => 'ok']);
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Views</h2>
            <div class="card card-large">
                <p>Views live in <code>views/</code> as <code>.php</code> files. Pass data via the second argument of <code>view()</code>. Use <code>\Core\Security::e()</code> for output escaping.</p>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Models (Eloquent)</h2>
            <div class="card card-large">
                <p>LaraHub uses Illuminate Eloquent. Models go in <code>app/Models/</code> with <code>$table</code>, <code>$fillable</code>, and relationships.</p>
                <div class="code-block">
public function user() { return $this->belongsTo(User::class); }
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Project Structure</h2>
            <div class="card card-large">
                <pre class="structure">framework/
├── <span class="dir">app/</span>
│   ├── Controllers/     # HTTP controllers
│   ├── Models/          # Eloquent models
│   └── Middlewares/     # Route middleware
├── <span class="dir">bootstrap/app.php</span>   # Application bootstrap
├── <span class="dir">core/</span>                # Router, Controller, ErrorHandler, Security, etc.
├── <span class="dir">database/migrations/</span>
├── <span class="dir">public/index.php</span>    # Web entry point
├── <span class="dir">routes/</span>             # web.php, api.php
├── <span class="dir">storage/</span>            # Logs, cache, uploads, keys
└── <span class="dir">views/</span>              # View templates</pre>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Configuration</h2>
            <div class="card card-large">
                <p>Environment variables in <code>.env</code>:</p>
                <table class="env-table">
                    <tr><th>Variable</th><th>Description</th></tr>
                    <tr><td>APP_NAME</td><td>Application name</td></tr>
                    <tr><td>APP_ENV</td><td>Environment (local, production)</td></tr>
                    <tr><td>APP_DEBUG</td><td>true = debug views, false = production error pages</td></tr>
                    <tr><td>APP_KEY</td><td>Generated with php larahub generate:key</td></tr>
                    <tr><td>DB_HOST, DB_NAME, DB_USER, DB_PASS</td><td>Database credentials</td></tr>
                    <tr><td>SESSION_DRIVER</td><td>Session driver (e.g. file)</td></tr>
                </table>
            </div>
        </section>
    </main>

    <footer class="footer">
        LaraHub &middot; Lightweight PHP Framework &middot; MIT License
    </footer>
</body>
</html>
