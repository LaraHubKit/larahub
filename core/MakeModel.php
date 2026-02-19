<?php

namespace Core;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Str;

class MakeModel
{
    protected string $basePath;
    protected string $modelName;
    protected bool $withMigration = false;
    protected ?string $blameableTable = 'users';

    public function __construct(string $basePath, string $modelName, array $options = [])
    {
        $this->basePath = rtrim($basePath, '/\\');
        $this->modelName = $modelName;
        $this->withMigration = $options['migration'] ?? $options['m'] ?? false;
        $this->blameableTable = !empty($options['no_blameable'])
            ? null
            : ($options['blameable'] ?? $options['b'] ?? 'users');
    }

    public function run(): array
    {
        $created = [];
        $modelPath = $this->basePath . '/app/Models/' . $this->modelName . '.php';

        if (file_exists($modelPath)) {
            throw new \RuntimeException("Model already exists: {$this->modelName}");
        }

        $this->ensureDir(dirname($modelPath));
        file_put_contents($modelPath, $this->getModelContent());
        $created[] = $modelPath;

        if ($this->withMigration) {
            $migrationPath = $this->createMigration();
            $created[] = $migrationPath;
        }

        return $created;
    }

    protected function getModelContent(): string
    {
        $table = $this->getTableName();
        return <<<PHP
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$this->modelName} extends Model
{
    protected \$table = '{$table}';
    protected \$fillable = [];
}
PHP;
    }

    protected function createMigration(): string
    {
        $migrationsDir = $this->basePath . '/database/migrations';
        $this->ensureDir($migrationsDir);

        $table = $this->getTableName();
        $className = 'Create' . Str::plural($this->modelName) . 'Table';
        $filename = date('Y_m_d_His') . '_create_' . $table . '_table.php';
        $path = $migrationsDir . '/' . $filename;

        $blameableCode = $this->getBlameableCode();

        $content = <<<PHP
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Capsule::schema()->create('{$table}', function (Blueprint \$table) {
            \$table->id();
            // ========== Modifiers ==========
            //   ->after('column')   ->before('column')   ->nullable()
            //   ->unique()          ->index()            ->fullText()
            //   ->default(\$value)  ->unsigned()         ->charset('utf8mb4')
            //   ->collation('...')  ->comment('...')     ->first()
            // ========== Integers ==========
            //   id(), increments(), bigIncrements(), integer(), bigInteger(),
            //   tinyInteger(), smallInteger(), mediumInteger(),
            //   unsignedInteger(), unsignedBigInteger(), unsignedTinyInteger(),
            //   unsignedSmallInteger(), unsignedMediumInteger()
            // ========== Strings ==========
            //   string(\$col, 255), char(\$col, 1), text(), tinyText(), mediumText(), longText()
            // ========== Numbers ==========
            //   float(), double(), decimal(8,2), boolean(), unsignedDecimal(8,2)
            // ========== Dates ==========
            //   date(), dateTime(), dateTimeTz(), time(), timeTz(), year(),
            //   timestamp(), timestampTz(), timestamps(), softDeletes()
            // ========== Network & Binary ==========
            //   ipAddress(), macAddress(), uuid(), ulid(), binary(), geometry()
            // ========== JSON & Sets ==========
            //   json(), jsonb(), enum(\$col, []), set(\$col, []), point(), lineString()
            // ========== Special ==========
            //   morphs(\$name), rememberToken(), foreignId(\$col)->constrained()->nullOnDelete()
            \$table->timestamps();
{$blameableCode}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Capsule::schema()->dropIfExists('{$table}');
    }
};
PHP;

        file_put_contents($path, $content);
        return $path;
    }

    protected function getBlameableCode(): string
    {
        if ($this->blameableTable === null) {
            return '';
        }
        $refTable = $this->blameableTable;
        return "            // Blameable: created_by, updated_by (foreign key to {$refTable})\n" .
               "            \$table->foreignId('created_by')->nullable()->constrained('{$refTable}')->nullOnDelete();\n" .
               "            \$table->foreignId('updated_by')->nullable()->constrained('{$refTable}')->nullOnDelete();";
    }

    protected function getTableName(): string
    {
        return Str::snake(Str::plural($this->modelName));
    }

    protected function ensureDir(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
