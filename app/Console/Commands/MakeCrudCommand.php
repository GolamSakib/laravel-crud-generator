<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {model} {--fields=} {--relations=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations for a model';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $model     = $this->argument('model');
        $fields    = $this->option('fields');
        $relations = $this->option('relations');

        $this->generateModel($model, $fields, $relations);
        $this->generateMigration($model, $fields);
        // $this->generateController($model);
        // $this->generateRequest($model, $fields);
        // $this->generateViews($model);
        // $this->generateRoutes($model);
        $this->info('CRUD operations generated successfully');
    }

    private function generateModel($model, $fields, $relations)
    {
        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{fillable}}',
                '{{relations}}',
            ],
            [
                $model,
                $this->generateFillable($fields),
                $this->generateRelations($relations),
            ],
            $this->getStub('Model')
        );

        if (! file_exists($path = app_path('/Models'))) {
            mkdir($path, 0777, true);
        }

        file_put_contents(app_path("/Models/{$model}.php"), $modelTemplate);
    }

    private function generateMigration($model, $fields)
    {
        $tableName = Str::plural(Str::snake($model));
        $fields    = $this->parseFields($fields);

        $migrationTemplate = str_replace(
            [
                '{{tableName}}',
                '{{fields}}',
            ],
            [
                $tableName,
                $this->generateMigrationFields($fields),
            ],
            $this->getStub('Migration')
        );

        $migrationFileName = date('Y_m_d_His') . "_create_{$tableName}_table.php";
        file_put_contents(database_path("/migrations/{$migrationFileName}"), $migrationTemplate);
    }

    private function parseFields($fields)
    {
        if (empty($fields)) {
            return [];
        }

        $parsedFields = [];

        // First, temporarily replace commas inside enum() with a special character
        $fields = preg_replace_callback('/enum\((.*?)\)/', function($matches) {
            return 'enum(' . str_replace(',', '|', $matches[1]) . ')';
        }, $fields);

        // Now split by comma
        $fields = explode(',', $fields);

        foreach ($fields as $field) {
            $parts = explode(':', trim($field));
            if (count($parts) !== 2) {
                continue;
            }

            $name = $parts[0];
            $type = $parts[1];

            // Replace back the special character with comma in enum values
            if (str_contains($type, 'enum(')) {
                $type = str_replace('|', ',', $type);
            }

            $parsedFields[$name] = $type;
        }

        return $parsedFields;
    }

    private function generateMigrationFields($fields)
    {
        $fieldsArray = [];
        foreach ($fields as $field => $type) {
            if (str_contains($type, 'enum')) {
                preg_match('/enum\((.*?)\)/', $type, $matches);
                $options       = explode(',', $matches[1]);
                $fieldsArray[] = "\$table->enum('$field', [" . implode(',', array_map(fn($opt) => "'$opt'", $options)) . "])";
            } else {
                $fieldsArray[] = "\$table->{$type}('$field')";
            }
        }
        return implode(";\n            ", $fieldsArray);
    }

    private function generateFillable($fields)
    {
        if (empty($fields)) {
            return '[]';
        }

        // First, temporarily replace commas inside enum() with a special character
        $fields = preg_replace_callback('/enum\((.*?)\)/', function($matches) {
            return 'enum(' . str_replace(',', '|', $matches[1]) . ')';
        }, $fields);

        // Now split by comma
        $fieldsArray = explode(',', $fields);
        $fillable = [];

        foreach ($fieldsArray as $field) {
            // Split field into name and type
            $parts = explode(':', trim($field));
            if (count($parts) === 2) {
                $fillable[] = "'" . trim($parts[0]) . "'";
            }
        }

        return implode(', ', $fillable);
    }

    private function generateRelations($relations)
    {
        if (empty($relations)) {
            return '';
        }

        // Parse the relations string (format: "posts:hasMany,profile:hasOne")
        $relationsArray  = explode(',', $relations);
        $relationMethods = [];

        foreach ($relationsArray as $relation) {
            // Split relation into name and type
            list($name, $type) = explode(':', $relation);

            $modelName         = Str::studly(Str::singular($name));
            $relationMethods[] = $this->generateRelationMethod($name, $type, $modelName);
        }

        return implode("\n\n    ", $relationMethods);
    }

    private function generateRelationMethod($name, $type, $modelName)
    {
        return "public function " . Str::camel($name) . "()
    {
        return \$this->{$type}(\\App\\Models\\{$modelName}::class);
    }";
    }

    private function getStub($type)
    {
        return file_get_contents(resource_path("stubs/$type.stub"));
    }


}
