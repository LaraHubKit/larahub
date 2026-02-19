<?php

$basePath = __DIR__ . '/..';

// storage folders (create first so keys dir exists before generate:key)
$dirs = ['storage/logs','storage/cache','storage/uploads','storage/keys','storage/public','storage/private','database/migrations'];
foreach ($dirs as $dir) {
    if (!is_dir($basePath . '/' . $dir)) {
        mkdir($basePath . '/' . $dir, 0777, true);
        echo "📁 Created $dir\n";
    }
}

// copy .env
$envExample = $basePath . '/.env.example';
$envFile = $basePath . '/.env';

$envJustCreated = false;
if (!file_exists($envFile) && file_exists($envExample)) {
    copy($envExample, $envFile);
    echo "✅ .env created from .env.example\n";
    $envJustCreated = true;
}

// when .env is newly created, run generate:key to save encrypted APP_KEY to both .env and storage
if ($envJustCreated) {
    passthru('php ' . escapeshellarg($basePath . '/larahub') . ' generate:key');
}