<?php

namespace Core;

class ErrorHandler
{
    protected static string $basePath;

    public static function register(string $basePath): void
    {
        self::$basePath = $basePath;

        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError'], E_ALL);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError(int $severity, string $message, string $file = '', int $line = 0): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    public static function handleException(\Throwable $e): void
    {
        self::report($e);
        self::render($e);
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE], true)) {
            self::handleException(new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            ));
        }
    }

    public static function report(\Throwable $e): void
    {
        $logDir = self::$basePath . '/storage/logs';
        if (is_dir($logDir) && is_writable($logDir)) {
            $logFile = $logDir . '/error-' . date('Y-m-d') . '.log';
            $entry = sprintf(
                "[%s] %s: %s in %s on line %d\n%s\n---\n",
                date('Y-m-d H:i:s'),
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );
            file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
        }
    }

    public static function render(\Throwable $e, int $code = 500): void
    {
        if (headers_sent() === false) {
            http_response_code($code);
            header('Content-Type: text/html; charset=UTF-8');
        }

        $debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $view = $debug ? 'debug' : 'production';

        $data = [
            'message'    => $e->getMessage(),
            'exception'  => get_class($e),
            'file'       => $e->getFile(),
            'line'       => $e->getLine(),
            'trace'      => $e->getTrace(),
            'traceAsStr' => $e->getTraceAsString(),
            'code'       => $code,
            'appName'    => $_ENV['APP_NAME'] ?? 'LaraHub',
        ];

        $viewPath = self::$basePath . "/views/errors/{$view}.php";
        if (file_exists($viewPath)) {
            extract($data);
            require $viewPath;
        } else {
            echo self::fallbackHtml($e, $debug, $code);
        }
    }

    public static function render404(): void
    {
        if (headers_sent() === false) {
            http_response_code(404);
            header('Content-Type: text/html; charset=UTF-8');
        }

        $debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $view = $debug ? '404-debug' : '404';

        $data = [
            'message' => 'Page not found',
            'uri'     => $_SERVER['REQUEST_URI'] ?? '/',
            'method'  => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'appName' => $_ENV['APP_NAME'] ?? 'LaraHub',
        ];

        $viewPath = self::$basePath . "/views/errors/{$view}.php";
        if (file_exists($viewPath)) {
            extract($data);
            require $viewPath;
        } else {
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>404</title></head><body><h1>404 Not Found</h1><p>', htmlspecialchars($data['uri']), '</p></body></html>';
        }
    }

    protected static function fallbackHtml(\Throwable $e, bool $debug, int $code): string
    {
        if ($debug) {
            return sprintf(
                '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error %d</title></head><body><h1>%s</h1><pre>%s</pre><pre>%s</pre></body></html>',
                $code,
                htmlspecialchars($e->getMessage()),
                htmlspecialchars($e->getFile() . ':' . $e->getLine()),
                htmlspecialchars($e->getTraceAsString())
            );
        }
        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title></head><body><h1>Something went wrong</h1><p>Please try again later.</p></body></html>';
    }
}
