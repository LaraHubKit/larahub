<?php

namespace Core;

/**
 * Compiles .hub.php templates with Blade-like directives.
 * Supports directives from description.txt.
 */
class ViewCompiler
{
    protected string $viewsPath;
    protected string $cachePath;

    public function __construct(string $basePath)
    {
        $this->viewsPath = $basePath . '/views';
        $this->cachePath = $basePath . '/storage/framework/views';
    }

    public function compile(string $path): string
    {
        $viewPath = str_replace('.', '/', $path) . '.hub.php';
        $fullPath = $this->viewsPath . '/' . $viewPath;

        if (!file_exists($fullPath)) {
            throw new \RuntimeException("View not found: {$path}");
        }

        $cacheFile = $this->cachePath . '/' . md5($fullPath) . '.php';
        $mtime = filemtime($fullPath);
        if (file_exists($cacheFile) && filemtime($cacheFile) >= $mtime) {
            return $cacheFile;
        }

        $code = file_get_contents($fullPath);
        $code = $this->compileDirectives($code);
        $code = $this->compileEchoes($code);

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
        file_put_contents($cacheFile, $code);

        return $cacheFile;
    }

    protected function compileDirectives(string $code): string
    {
        $directives = [
            // Layout
            '/@extends\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '<?php $__extends = \'$1\'; ?>',
            '/@section\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '<?php ob_start(); $__sectionName = \'$1\'; ?>',
            '/@endsection/' => '<?php if (!isset($__sections)) $__sections = []; $__sections[$__sectionName ?? \'content\'] = ob_get_clean(); unset($__sectionName); ?>',
            '/@yield\s*\(\s*[\'"]([^\'"]+)[\'"]\s*(?:,\s*(.+?))?\s*\)/' => '<?= $__sections[\'$1\'] ?? $2 ?? \'\' ?>',

            // Partials (with-data pattern first; use compile() so nested views get compiled)
            '/@include\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*([^)]+)\)/' => '<?php extract($2); require $__compiler->compile(\'$1\'); ?>',
            '/@include\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '<?php require $__compiler->compile(\'$1\'); ?>',
            '/<x-([a-zA-Z0-9.-]+)\s*\/>/' => '<?php require $__compiler->compile(\'components.$1\'); ?>',

            // Conditions & Loops
            '/@if\s*\((.*?)\)\s*$/m' => '<?php if ($1): ?>',
            '/@elseif\s*\((.*?)\)\s*$/m' => '<?php elseif ($1): ?>',
            '/@else\s*$/m' => '<?php else: ?>',
            '/@endif\s*$/m' => '<?php endif; ?>',
            '/@foreach\s*\((.*?)\)\s*$/m' => '<?php foreach ($1): ?>',
            '/@endforeach\s*$/m' => '<?php endforeach; ?>',
            '/@forelse\s*\((.*?)\)\s*$/m' => '<?php $__empty = true; foreach ($1): $__empty = false; ?>',
            '/@empty\s*$/m' => '<?php endforeach; if ($__empty): ?>',
            '/@endforelse\s*$/m' => '<?php endif; unset($__empty); ?>',

            // Auth
            '/@auth\s*$/m' => '<?php if (!empty($_SESSION[\'user_id\'] ?? null)): ?>',
            '/@endauth\s*$/m' => '<?php endif; ?>',
            '/@guest\s*$/m' => '<?php if (empty($_SESSION[\'user_id\'] ?? null)): ?>',
            '/@endguest\s*$/m' => '<?php endif; ?>',

            // Forms & Security
            '/@csrf\s*$/m' => '<?= \Core\Security::csrfField() ?>',
            '/@method\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '<input type="hidden" name="_method" value="$1">',
            '/@error\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*$/m' => '<?php if (isset($errors[\'$1\'])): $message = $errors[\'$1\']; ?>',
            '/@enderror\s*$/m' => '<?php endif; ?>',

            // Stacks
            '/@push\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*$/m' => '<?php ob_start(); $__pushName = \'$1\'; ?>',
            '/@endpush\s*$/m' => '<?php if (!isset($__stacks)) $__stacks = []; if (!isset($__stacks[$__pushName])) $__stacks[$__pushName] = []; $__stacks[$__pushName][] = ob_get_clean(); unset($__pushName); ?>',
            '/@stack\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '<?php foreach (($__stacks[\'$1\'] ?? []) as $__s): echo $__s; endforeach; ?>',

            // LaraHub extras
            '/@lhAuth\s*$/m' => '<?php if (!empty($_SESSION[\'user_id\'] ?? null)): ?>',
            '/@endLhAuth\s*$/m' => '<?php endif; ?>',
            '/@lhGuest\s*$/m' => '<?php if (empty($_SESSION[\'user_id\'] ?? null)): ?>',
            '/@endLhGuest\s*$/m' => '<?php endif; ?>',
            '/@lhEnv\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*$/m' => '<?php if ((env(\'APP_ENV\', \'production\')) === \'$1\'): ?>',
            '/@endLhEnv\s*$/m' => '<?php endif; ?>',
            '/@lhTitle\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '<?php $__pageTitle = \'$1\'; ?>',
            '/@lhAsset\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '<?= lh_asset(\'$1\') ?>',
            '/@lhRoute\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '<?= lh_route(\'$1\') ?>',
        ];

        foreach ($directives as $pattern => $replacement) {
            $code = preg_replace($pattern, $replacement, $code);
        }

        return $code;
    }

    protected function compileEchoes(string $code): string
    {
        // {{ expr }} - escaped
        $code = preg_replace('/\{\{\s*(.+?)\s*\}\}/s', '<?= \Core\Security::e($1) ?>', $code);
        // {!! expr !!} - unescaped
        $code = preg_replace('/\{!!\s*(.+?)\s*!!\}/s', '<?= $1 ?>', $code);
        return $code;
    }

    public function resolveView(string $path): string
    {
        $viewPath = str_replace('.', '/', $path) . '.hub.php';
        $fullPath = $this->viewsPath . '/' . $viewPath;
        if (file_exists($fullPath)) {
            return $fullPath;
        }
        throw new \RuntimeException("View not found: {$path}");
    }

    public function render(string $path, array $data = []): string
    {
        $__compiler = $this;
        $__sections = [];
        $__stacks = [];
        $cacheFile = $this->compile($path);

        extract(array_merge($data, ['__compiler' => $this]));

        ob_start();
        require $cacheFile;
        $content = ob_get_clean();

        if (isset($__extends)) {
            $__sections = $__sections ?? [];
            $layoutPath = $this->compile($__extends);
            ob_start();
            require $layoutPath;
            return ob_get_clean();
        }

        return $content;
    }
}
