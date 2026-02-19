<?php

namespace Core;

/**
 * Storage - Public and private file storage
 *
 * public()  - files save to storage/public  - accessible with or without auth
 * private() - files save to storage/private - only when authenticated
 *
 * Usage:
 *   Storage::public()->disk('local')->folder('uploads')->put('file.jpg', $content);
 *   Storage::private()->disk('local')->folder('documents')->put('secret.pdf', $content);
 */
class Storage
{
    private const VISIBILITY_PUBLIC  = 'public';
    private const VISIBILITY_PRIVATE = 'private';

    private string $visibility;
    private string $disk = 'local';
    private string $folder = '';
    private ?array $config = null;
    private string $basePath;

    private function __construct(string $visibility)
    {
        $this->visibility = $visibility;
        $this->basePath   = dirname(__DIR__);
    }

    public static function public(): self
    {
        return new self(self::VISIBILITY_PUBLIC);
    }

    public static function private(): self
    {
        return new self(self::VISIBILITY_PRIVATE);
    }

    public function disk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    public function folder(string $folder): StorageFolder
    {
        $this->folder = trim($folder, '/\\');
        return new StorageFolder($this);
    }

    public function getConfig(): array
    {
        if ($this->config === null) {
            $this->config = function_exists('config') ? config('filesystem') : $this->loadConfigFile();
        }
        return $this->config ?: $this->defaultConfig();
    }

    private function loadConfigFile(): array
    {
        $path = $this->basePath . '/config/filesystem.php';
        return file_exists($path) ? require $path : [];
    }

    private function defaultConfig(): array
    {
        return [
            'disks' => [
                'local' => ['driver' => 'local', 'root' => $this->basePath . '/storage'],
            ],
            'visibility' => ['public' => 'public', 'private' => 'private'],
        ];
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function getDisk(): string
    {
        return $this->disk;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function ensurePrivateAuth(): void
    {
        if ($this->visibility === self::VISIBILITY_PRIVATE && !isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            header('Location: /login');
            exit;
        }
    }
}
