<?php

// copy .env
$envExample = __DIR__ . '/../.env.example';
$envFile = __DIR__ . '/../.env';

if (!file_exists($envFile) && file_exists($envExample)) {
    copy($envExample, $envFile);
    echo "✅ .env created from .env.example\n";
}

// generate APP_KEY
$env = file_get_contents($envFile);
if (strpos($env, 'APP_KEY=') !== false && !str_contains($env, 'APP_KEY=base64:')) {
    $key = 'base64:' . base64_encode(random_bytes(32));
    $env = preg_replace('/APP_KEY=.*/', 'APP_KEY='.$key, $env);
    file_put_contents($envFile, $env);
    echo "🔐 APP_KEY generated\n";
}

// storage folders
$dirs = ['storage/logs','storage/cache','storage/uploads','storage/keys'];
foreach ($dirs as $dir) {
    if (!is_dir(__DIR__ . '/../' . $dir)) {
        mkdir(__DIR__ . '/../' . $dir, 0777, true);
        echo "📁 Created $dir\n";
    }
}