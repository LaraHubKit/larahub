<?php

$basePath = __DIR__ . '/..';

// storage + database folders (create at project creation)
$dirs = ['storage/logs','storage/cache','storage/uploads','storage/keys','storage/public','storage/private','storage/framework/views','database','database/migrations'];
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
$keyGenerated = false;
if ($envJustCreated) {
    $returnVar = 0;
    passthru('php ' . escapeshellarg($basePath . '/larahub') . ' generate:key', $returnVar);
    $keyGenerated = ($returnVar === 0);
}

if ($keyGenerated) {
    echo "\n✅ Project created successfully!\n";
}