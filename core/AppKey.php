<?php

namespace Core;

class AppKey
{
    private const CIPHER = 'aes-256-cbc';
    private const IV_LEN = 16;
    private const MASTER_LEN = 32;

    public static function keysDir(string $basePath): string
    {
        return $basePath . '/storage/keys';
    }

    public static function masterPath(string $basePath): string
    {
        return self::keysDir($basePath) . '/.master';
    }

    public static function storagePath(string $basePath): string
    {
        return self::keysDir($basePath) . '/app_key';
    }

    public static function envSignaturePath(string $basePath): string
    {
        return self::keysDir($basePath) . '/.env_signature';
    }

    public static function getOrCreateMasterKey(string $basePath): string
    {
        $dir = self::keysDir($basePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = self::masterPath($basePath);
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        $master = random_bytes(self::MASTER_LEN);
        file_put_contents($path, $master);
        return $master;
    }

    public static function encrypt(string $plain, string $master): string
    {
        $iv = random_bytes(self::IV_LEN);
        $raw = openssl_encrypt($plain, self::CIPHER, $master, OPENSSL_RAW_DATA, $iv);
        if ($raw === false) {
            throw new \RuntimeException('Encryption failed');
        }
        return base64_encode($iv . $raw);
    }

    public static function decrypt(string $encoded, string $master): ?string
    {
        $bin = base64_decode($encoded, true);
        if ($bin === false || strlen($bin) < self::IV_LEN) {
            return null;
        }
        $iv = substr($bin, 0, self::IV_LEN);
        $ciphertext = substr($bin, self::IV_LEN);
        $plain = openssl_decrypt($ciphertext, self::CIPHER, $master, OPENSSL_RAW_DATA, $iv);
        return $plain !== false ? $plain : null;
    }

    public static function hashKey(string $key): string
    {
        return hash('sha256', $key);
    }

    /** Detect if .env file content has changed (any change). */
    public static function envSignature(string $envPath): string
    {
        if (!file_exists($envPath)) {
            return '';
        }
        return hash_file('sha256', $envPath);
    }
}
