<?php

namespace Core;

/**
 * StorageFolder - File operations within a storage folder
 *
 * Returned by Storage::public()->disk('local')->folder('uploads')
 */
class StorageFolder
{
    private Storage $storage;
    private string $fullPath;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
        $this->storage->ensurePrivateAuth();

        $config = $this->storage->getConfig();
        $disk   = $config['disks'][$this->storage->getDisk()] ?? $config['disks']['local'];
        $root   = $disk['root'] ?? dirname(__DIR__) . '/storage';
        $vis    = $config['visibility'][$this->storage->getVisibility()] ?? $this->storage->getVisibility();
        $folder = $this->storage->getFolder();

        $this->fullPath = rtrim($root . '/' . $vis . ($folder ? '/' . $folder : ''), '/');
    }

    /**
     * Put file from PHP upload ($_FILES['field'])
     */
    public function putFile(array $upload, ?string $filename = null): ?string
    {
        if (($upload['error'] ?? 0) !== UPLOAD_ERR_OK) {
            return null;
        }
        $name = $filename ?? basename($upload['name']);
        return $this->put($name, file_get_contents($upload['tmp_name'])) ? $name : null;
    }

    public function put(string $filename, string $contents): bool
    {
        $path = $this->path($filename);
        $dir  = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return file_put_contents($path, $contents) !== false;
    }

    public function get(string $filename): ?string
    {
        $path = $this->path($filename);
        return file_exists($path) && is_file($path) ? file_get_contents($path) : null;
    }

    public function exists(string $filename): bool
    {
        return file_exists($this->path($filename)) && is_file($this->path($filename));
    }

    public function delete(string $filename): bool
    {
        $path = $this->path($filename);
        if (file_exists($path) && is_file($path)) {
            return unlink($path);
        }
        return false;
    }

    public function path(string $filename = ''): string
    {
        $filename = ltrim(str_replace(['..', '\\'], ['', '/'], $filename), '/');
        return $filename === '' ? $this->fullPath : $this->fullPath . '/' . $filename;
    }

    public function fullPath(): string
    {
        return $this->fullPath;
    }
}
