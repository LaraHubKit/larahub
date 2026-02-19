# LaraHub

A lightweight PHP framework for building small to medium web applications. Built with simplicity and developer experience in mind.

---

## Requirements

- **PHP** 8.1 or higher
- **Composer**
- **MySQL** (or compatible database)

---

## Installation

```bash
# Clone or download the framework
composer install

# Configure environment
cp .env.example .env
# Edit .env with your database credentials and settings

# Generate application key (required before running)
php larahub generate:key
```

Setup runs automatically on `composer install` and will:
- Copy `.env.example` to `.env` if missing
- Generate `APP_KEY` if not set
- Create `storage/logs`, `storage/cache`, `storage/uploads`, and `storage/keys`

---

## Quick Start

```bash
# Start development server
php larahub run
```

Open **http://localhost:8000** in your browser.

---

## Project Structure

```
framework/
├── app/
│   ├── Controllers/     # HTTP controllers
│   ├── Models/          # Eloquent models
│   └── Middlewares/     # Route middleware
├── bootstrap/
│   └── app.php          # Application bootstrap
├── core/                # Framework core
│   ├── Router.php       # Request routing
│   ├── Controller.php   # Base controller
│   ├── ErrorHandler.php # Error & exception handling
│   ├── Security.php     # CSRF, escaping
│   ├── AppKey.php       # Encrypted key management
│   ├── MakeModel.php    # Model & migration generator
│   └── Setup.php        # Initial setup
├── database/
│   └── migrations/      # Database migrations
├── public/
│   └── index.php        # Web entry point
├── routes/
│   ├── web.php          # Web routes
│   └── api.php          # API routes
├── storage/             # Logs, cache, uploads, keys
├── views/               # View templates
└── larahub              # CLI entry point
```

---

## CLI Commands

| Command | Description |
|---------|-------------|
| `php larahub run` | Start built-in dev server on `http://localhost:8000` |
| `php larahub migrate` | Run all database migrations |
| `php larahub make:controller Name` | Create a new controller |
| `php larahub make:model Name` | Create a new model |
| `php larahub make:model Name -m` | Create model with migration |
| `php larahub make:model Name -m -b=users` | With migration + blameable columns |
| `php larahub make:model Name -m --no-blameable` | Model + migration, no blameable |
| `php larahub generate:key` | Generate or regenerate `APP_KEY` |

---

## Routing

Define routes in `routes/web.php` and `routes/api.php`:

```php
// GET route
$router->get('/about', [\App\Controllers\AboutController::class, 'index']);

// POST route
$router->post('/contact', [\App\Controllers\ContactController::class, 'submit']);

// With middleware
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'], ['AuthMiddleware']);
```

---

## Controllers

Extend `Core\Controller` and return view output or JSON:

```php
<?php

namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->view('home', ['title' => 'Welcome']);
    }

    public function api()
    {
        return $this->json(['status' => 'ok']);
    }
}
```

---

## Views

Views live in `views/` as `.php` files. Pass data via the second argument of `view()`:

```php
return $this->view('users.profile', ['user' => $user]);
```

Use `Core\Security::e()` for output escaping:

```php
<?= \Core\Security::e($user->name) ?>
```

---

## Models (Eloquent)

LaraHub uses [Illuminate Eloquent](https://laravel.com/docs/eloquent). Models go in `app/Models/`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = ['title', 'content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## Migrations

Migrations are in `database/migrations/`. The `make:model` command can create them with useful defaults.

**Blueprint modifiers (in migration comments):**
- Modifiers: `->after()`, `->nullable()`, `->unique()`, `->index()`, `->default()`
- Types: `string()`, `text()`, `integer()`, `timestamp()`, `foreignId()`, etc.

**Blameable option:** Add `created_by` and `updated_by` foreign keys:

```bash
php larahub make:model Article -m -b=users
```

---

## Middleware

Create middleware in `app/Middlewares/` with a `handle()` method:

```php
<?php

namespace App\Middlewares;

class AuthMiddleware
{
    public function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}
```

---

## Security

- **CSRF protection:** Add `<?= \Core\Security::csrfField() ?>` in forms, then verify with `\Core\Security::verifyCsrf($_POST['csrf'] ?? '')` before processing.
- **Output escaping:** Use `\Core\Security::e($value)` for user-provided data in views.
- **APP_KEY:** Stored encrypted in `.env`, decrypted using a master key in `storage/keys/`. Run `php larahub generate:key` after config changes if needed.

---

## Configuration

Environment variables (`.env`):

| Variable | Description |
|----------|-------------|
| `APP_NAME` | Application name |
| `APP_ENV` | Environment (local, production, etc.) |
| `APP_DEBUG` | `true` = debug views, `false` = production error pages |
| `APP_KEY` | Generated with `php larahub generate:key` |
| `DB_HOST` | Database host |
| `DB_NAME` | Database name |
| `DB_USER` | Database user |
| `DB_PASS` | Database password |
| `SESSION_DRIVER` | Session driver (e.g. `file`) |

---

## Error Handling

- **Debug mode** (`APP_DEBUG=true`): Stack traces and detailed errors
- **Production** (`APP_DEBUG=false`): Generic error pages, details logged to `storage/logs/`
- 404 pages rendered when no route matches

---

## License

MIT
