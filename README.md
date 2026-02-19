# LaraHub

A simple PHP framework for small to medium web apps. Easy to learn and quick to build with.

---

## What You Need

- PHP 8.1+
- Composer
- MySQL (or compatible)

---

## Get Started (3 Steps)

### 1. Create your project

```bash
composer create-project larahub/larahub my-app
cd my-app
```

Setup runs automatically: creates folders, copies `.env`, generates `APP_KEY`.

### 2. Configure database

Edit `.env` and set your database details:

```
DB_HOST=127.0.0.1
DB_NAME=your_database
DB_USER=your_user
DB_PASS=your_password
```

### 3. Run it

```bash
php larahub migrate    # Run migrations (first time)
php larahub run        # Start server
```

Open **http://localhost:8000** in your browser.

---

## CLI Commands

| Command | What it does |
|---------|--------------|
| `php larahub run` | Start dev server |
| `php larahub migrate` | Run migrations |
| `php larahub migrate:fresh` | Drop tables and re-run migrations |
| `php larahub make:controller Name` | Create a controller |
| `php larahub make:view name` | Create a .hub.php view (use dots: users.profile) |
| `php larahub make:model Name` | Create a model |
| `php larahub make:model Name -m` | Create model + migration |
| `php larahub make:model Name -m -b=users` | Model + migration + blameable (created_by, updated_by) |
| `php larahub generate:key` | Generate new APP_KEY |

---

## Project Structure

```
my-app/
├── app/
│   ├── Controllers/    # Your controllers
│   ├── Models/         # Eloquent models
│   └── Middlewares/    # Route middleware
├── database/migrations/ # Database migrations
├── public/             # Web root (index.php)
├── routes/
│   ├── web.php        # Web routes
│   └── api.php        # API routes
├── storage/           # Logs, cache, uploads, keys
├── views/             # .hub.php view templates
└── larahub            # CLI tool
```

---

## Routing

Add routes in `routes/web.php`:

```php
$router->get('/about', [\App\Controllers\AboutController::class, 'index']);
$router->post('/contact', [\App\Controllers\ContactController::class, 'submit']);
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'], ['AuthMiddleware']);
```

---

## Controllers

Extend `Core\Controller`:

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

Views are `.hub.php` template files in `views/`. Use dots for subfolders: `users.profile` → `views/users/profile.hub.php`.

```php
return $this->view('home', ['user' => $user]);
```

Escape output in templates:

```php
<?= \Core\Security::e($user->name) ?>
```

---

## Models

Uses [Eloquent](https://laravel.com/docs/eloquent). Models go in `app/Models/`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## Middleware

Create in `app/Middlewares/`:

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

## Security Tips

- **Forms:** Add `<?= \Core\Security::csrfField() ?>` and verify with `\Core\Security::verifyCsrf($_POST['csrf'] ?? '')`
- **Output:** Use `\Core\Security::e($value)` for user data in views
- **APP_KEY:** Auto-generated. Regenerate with `php larahub generate:key` if needed

---

## Environment (.env)

| Variable | Purpose |
|----------|---------|
| `APP_NAME` | App name |
| `APP_DEBUG` | `true` = detailed errors, `false` = production mode |
| `APP_KEY` | Auto-generated, keep secret |
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` | Database connection |
| `SESSION_DRIVER` | e.g. `file` |

---

## Alternative Install (Clone)

```bash
git clone https://github.com/larahub/larahub.git my-app
cd my-app
composer install
```

Setup runs automatically on install.

---

## License

MIT
