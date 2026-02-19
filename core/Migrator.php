<?php

namespace Core;

use Illuminate\Database\Capsule\Manager as Capsule;

class Migrator
{
    protected string $basePath;
    protected string $migrationsTable = 'migrations';

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
    }

    /** Ensure migrations table exists. */
    public function ensureMigrationsTable(): void
    {
        if (Capsule::schema()->hasTable($this->migrationsTable)) {
            return;
        }
        Capsule::schema()->create($this->migrationsTable, function ($table) {
            $table->id();
            $table->string('file');
            $table->string('table')->nullable();
            $table->json('columns')->nullable();
            $table->timestamps();
        });
    }

    /** Get list of already-run migration files. */
    public function getRan(): array
    {
        $this->ensureMigrationsTable();
        return Capsule::table($this->migrationsTable)->pluck('file')->toArray();
    }

    /** Extract table name from migration filename (e.g. create_posts_table -> posts). */
    public function getTableFromFilename(string $filename): ?string
    {
        $base = basename($filename, '.php');
        if (preg_match('/create_(.+)_table$/', $base, $m)) {
            return $m[1];
        }
        if (preg_match('/add_.+_to_(.+)_table$/', $base, $m)) {
            return $m[1];
        }
        if (preg_match('/.+_table$/', $base, $m)) {
            return preg_replace('/_table$/', '', $base);
        }
        return null;
    }

    /** Get column names for a table from information_schema. */
    public function getColumnsForTable(string $table): array
    {
        $db = env('DB_NAME');
        $rows = Capsule::select(
            "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION",
            [$db, $table]
        );
        return array_column($rows, 'COLUMN_NAME');
    }

    /** Record a migration in the tracking table. */
    public function record(string $file, ?string $table, array $columns = []): void
    {
        $this->ensureMigrationsTable();
        Capsule::table($this->migrationsTable)->insert([
            'file'       => $file,
            'table'      => $table,
            'columns'    => json_encode($columns),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /** Run pending migrations. */
    public function migrate(): int
    {
        $this->ensureMigrationsTable();
        $ran = $this->getRan();
        $dir = $this->basePath . '/database/migrations';
        $files = glob($dir . '/*.php') ?: [];
        sort($files);

        $count = 0;
        foreach ($files as $file) {
            $filename = basename($file);
            if (in_array($filename, $ran, true)) {
                continue;
            }
            $migration = require $file;
            if (is_object($migration) && method_exists($migration, 'up')) {
                $migration->up();
                $table = $this->getTableFromFilename($filename);
                $columns = $table ? $this->getColumnsForTable($table) : [];
                $this->record($filename, $table, $columns);
                $count++;
                echo "  ✓ {$filename}\n";
            }
        }
        return $count;
    }

    /** Drop all tables and re-run all migrations. */
    public function migrateFresh(): void
    {
        $db = env('DB_NAME');
        Capsule::connection()->getPdo()->exec("SET FOREIGN_KEY_CHECKS = 0");
        $tables = Capsule::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?", [$db]);
        foreach ($tables as $row) {
            $name = $row->TABLE_NAME;
            Capsule::schema()->dropIfExists($name);
        }
        Capsule::connection()->getPdo()->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "🗑 Dropped all tables\n";
        $count = $this->migrate();
        echo "✅ Migrated {$count} file(s)\n";
    }
}
