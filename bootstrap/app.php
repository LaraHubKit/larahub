<?php

require __DIR__ . '/../vendor/autoload.php';

use Core\AppKey;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

$basePath = __DIR__ . '/..';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load env
$dotenv = Dotenv::createImmutable($basePath);
$dotenv->load();

// Helpers (env, config)
require __DIR__ . '/helpers.php';

// Error handling for web requests (APP_DEBUG: true = debug mode, false = production)
if (PHP_SAPI !== 'cli') {
    \Core\ErrorHandler::register($basePath);
}

// Ensure APP_KEY is set for every request
if (empty(env('APP_KEY'))) {
    http_response_code(500);
    echo 'Application key is missing. Run: php larahub generate:key';
    exit;
}

$envPath = $basePath . '/.env';
$masterPath = AppKey::masterPath($basePath);
$keyStoragePath = AppKey::storagePath($basePath);
$signaturePath = AppKey::envSignaturePath($basePath);

// Master key required to decrypt .env key
if (!file_exists($masterPath)) {
    http_response_code(500);
    echo 'Key not initialized. Run: php larahub generate:key';
    exit;
}
$master = file_get_contents($masterPath);

// Decrypt APP_KEY from .env (encrypted value) to get original key
$originalKey = AppKey::decrypt(trim(env('APP_KEY')), $master);
if ($originalKey === null) {
    http_response_code(500);
    echo 'Invalid or legacy APP_KEY. Run: php larahub generate:key';
    exit;
}

$currentSignature = AppKey::envSignature($envPath);
$envChanged = false;
if (file_exists($signaturePath)) {
    $storedSignature = trim(file_get_contents($signaturePath));
    if ($storedSignature !== $currentSignature) {
        $envChanged = true;
    }
} else {
    $envChanged = true;
}

$keyHash = AppKey::hashKey($originalKey);
$storedHash = file_exists($keyStoragePath) ? trim(file_get_contents($keyStoragePath)) : '';

$keyMismatch = !hash_equals($storedHash, $keyHash);

if ($envChanged || $keyMismatch) {
    // .env or key changed → treat as application restart
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }
    if (!is_dir(dirname($keyStoragePath))) {
        mkdir(dirname($keyStoragePath), 0777, true);
    }
    file_put_contents($keyStoragePath, $keyHash);
    file_put_contents($signaturePath, $currentSignature);

    if (PHP_SAPI === 'cli') {
        echo "Configuration changed. Application restarted.\n";
    } else {
        error_log('LaraHub: Configuration changed. Application restarted.');
        http_response_code(503);
        header('Retry-After: 2');
        echo 'Application configuration has changed. Please retry in a moment.';
    }
    exit;
}

// DB
$capsule = new Capsule;
$capsule->addConnection([
    'driver'   => 'mysql',
    'host'     => env('DB_HOST'),
    'database' => env('DB_NAME'),
    'username' => env('DB_USER'),
    'password' => env('DB_PASS', ''),
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// CSRF token
if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}