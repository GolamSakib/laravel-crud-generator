<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;


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

    private $modelVariable;

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
        $this->generateController($model, 'web');
        $this->generateController($model, 'api');
        $this->generateRequest($model, $fields);
        $this->generateViews($model, $fields);
        $this->generateRoutes($model);
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

private function generateController($model, $type = 'web')
{
    $modelPlural = Str::plural($model);
    $modelLower = Str::plural(Str::snake($model));
    $modelPluralLower = Str::plural($modelLower);
    $modelSingularLower = Str::snake($model);

    $controllerTemplate = str_replace(
        [
            '{{modelName}}',
            '{{modelNamePlural}}',
            '{{modelNameLower}}',
            '{{modelNamePluralLower}}',
            '{{modelNameSingularLower}}'
        ],
        [
            $model,
            $modelPlural,
            $modelLower,
            $modelPluralLower,
            $modelSingularLower
        ],
        $this->getStub($type === 'web' ? 'WebController' : 'ApiController')
    );

    $path = app_path('/Http/Controllers');
    if ($type === 'api') {
        $path .= '/Api';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    file_put_contents("$path/{$model}Controller.php", $controllerTemplate);
}

    private function generateRequest($model, $fields)
    {
        $requestTemplate = str_replace(
            [
                '{{modelName}}',
                '{{rules}}',
            ],
            [
                $model,
                $this->generateValidationRules($fields),
            ],
            $this->getStub('Request')
        );

        if (! file_exists($path = app_path('/Http/Requests'))) {
            mkdir($path, 0777, true);
        }

        file_put_contents(app_path("/Http/Requests/{$model}Request.php"), $requestTemplate);
    }

    private function generateViews($model, $fields)
    {
        $viewPath = resource_path("views/" . Str::plural(Str::snake($model)));

        if (! file_exists($viewPath)) {
            mkdir($viewPath, 0777, true);
        }

        $this->modelVariable = Str::camel($model);

        // Parse the fields string into an array
        $parsedFields = $this->parseFields($fields);

        // Generate each view
        foreach (['index', 'create', 'edit', 'show'] as $view) {
            $content = str_replace(
                [
                    '{{modelName}}',
                    '{{modelNamePlural}}',
                    '{{modelNameLower}}',
                    '{{formFields}}',
                    '{{showFields}}',
                    '{{tableHeaders}}',
                    '{{tableRows}}',
                ],
                [
                    $model,
                    Str::lower(Str::plural($model)),
                    Str::snake($model),
                    $this->generateFormFields($parsedFields),
                    $this->generateShowFields($parsedFields),
                    $this->generateTableHeaders($parsedFields),
                    $this->generateTableRows($parsedFields, $this->modelVariable),
                ],
                $this->getStub("views/$view")
            );

            file_put_contents("$viewPath/$view.blade.php", $content);
        }
    }

private function generateRoutes($model)
{
    $modelLower = Str::lower($model);
    $modelPlural = Str::plural($modelLower);

    // Generate web routes
    $webRouteTemplate = "
Route::resource('{$modelPlural}', App\Http\Controllers\\{$model}Controller::class);";

    File::append(
        base_path('routes/web.php'),
        $webRouteTemplate
    );

    // Generate API routes
    $apiRouteTemplate = "
Route::apiResource('{$modelPlural}', App\Http\Controllers\Api\\{$model}Controller::class);";

    File::append(
        base_path('routes/api.php'),
        $apiRouteTemplate
    );
}

    private function generateTableHeaders($fields)
    {
        $headers = [];
        foreach ($fields as $field => $type) {
            if ($field === 'id') {
                continue;
            }
            // Skip ID as it's already included
            $headers[] = '<th>' . ucwords(str_replace('_', ' ', $field)) . '</th>';
        }
        return implode("\n                ", $headers);
    }

    private function generateTableRows($fields, $modelVariable)
    {
        $rows = [];
        foreach ($fields as $field => $type) {
            if ($field === 'id') {
                continue;
            }
            // Skip ID as it's already included
            $rows[] = '<td>{{ $' . $modelVariable . '->' . $field . ' }}</td>';
        }
        return implode("\n                    ", $rows);
    }

    private function generateFormFields($fields)
    {
        $formFields = [];

        foreach ($fields as $field => $type) {
            if ($field === 'id') {
                continue;
            }
            // Skip ID field

            $label = ucwords(str_replace('_', ' ', $field));

            if (str_contains($type, 'enum')) {
                preg_match('/enum\((.*?)\)/', $type, $matches);
                $options = explode(',', $matches[1]);

                $formFields[] = $this->generateEnumField($field, $label, $options);
            } else {
                $formFields[] = $this->generateInputField($field, $label, $type);
            }
        }

        return implode("\n\n                        ", $formFields);
    }

    private function generateInputField($field, $label, $type)
    {
        // Replace match expression with if/elseif or switch
        if (in_array($type, ['integer', 'bigInteger', 'decimal'])) {
            $inputType = 'number';
        } elseif ($type === 'text') {
            $inputType = 'textarea';
        } else {
            $inputType = 'text';
        }

        if ($inputType === 'textarea') {
            return <<<HTML
        <div class="form-group row">
            <label for="$field" class="col-md-4 col-form-label text-md-right">$label</label>
            <div class="col-md-6">
                <textarea id="$field" class="form-control @error('$field') is-invalid @enderror" name="$field" rows="3">{{ old('$field', \${$this->modelVariable}->$field ?? '') }}</textarea>
                @error('$field')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ \$message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        HTML;
        }

        return <<<HTML
    <div class="form-group row">
        <label for="$field" class="col-md-4 col-form-label text-md-right">$label</label>
        <div class="col-md-6">
            <input id="$field" type="$inputType" class="form-control @error('$field') is-invalid @enderror" name="$field" value="{{ old('$field', \${$this->modelVariable}->$field ?? '') }}" required>
            @error('$field')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ \$message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    HTML;
    }

    private function generateEnumField($field, $label, $options)
    {
        $optionsHtml = [];
        foreach ($options as $option) {
            $option        = trim($option);
            $optionsHtml[] = <<<HTML
                            <option value="$option" {{ old('$field', \${$this->modelVariable}->$field ?? '') == '$option' ? 'selected' : '' }}>
                                {{ ucfirst('$option') }}
                            </option>
HTML;
        }

        $optionsString = implode("\n", $optionsHtml);

        return <<<HTML
    <div class="form-group row">
        <label for="$field" class="col-md-4 col-form-label text-md-right">$label</label>
        <div class="col-md-6">
            <select id="$field" class="form-control @error('$field') is-invalid @enderror" name="$field" required>
                <option value="">Select $label</option>
                $optionsString
            </select>
            @error('$field')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ \$message }}</strong>
                </span>
            @enderror
        </div>
    </div>
HTML;
    }

    private function generateShowFields($fields)
    {
        $showFields = [];

        foreach ($fields as $field => $type) {
            if ($field === 'id') {
                continue;
            }

            $label = ucwords(str_replace('_', ' ', $field));

            $showFields[] = <<<HTML
        <div class="form-group row">
            <label class="col-md-4 text-md-right">$label:</label>
            <div class="col-md-6">
                <p class="form-control-static">{{ \${$this->modelVariable}->$field }}</p>
            </div>
        </div>
        HTML;
        }

        return implode("\n\n                    ", $showFields);
    }

    private function generateValidationRules($fields)
    {
        $rules  = [];
        $fields = $this->parseFields($fields);

        foreach ($fields as $field => $type) {
            $rule = ['required'];

            if (str_contains($type, 'string')) {
                $rule[] = 'string';
                $rule[] = 'max:255';
            } elseif (str_contains($type, 'enum')) {
                preg_match('/enum\((.*?)\)/', $type, $matches);
                $options = explode(',', $matches[1]);
                $rule[]  = 'in:' . implode(',', $options);
            }

            $rules[] = "'$field' => ['" . implode("', '", $rule) . "']";
        }

        return implode(",\n            ", $rules);
    }

    private function parseFields($fields)
    {
        if (empty($fields)) {
            return [];
        }

        $parsedFields = [];

        $fields = preg_replace_callback('/enum\((.*?)\)/', function ($matches) {
            return 'enum(' . str_replace(',', '|', $matches[1]) . ')';
        }, $fields);

        $fields = explode(',', $fields);

        foreach ($fields as $field) {
            $parts = explode(':', trim($field));
            if (count($parts) !== 2) {
                continue;
            }

            $name = $parts[0];
            $type = $parts[1];

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

        $fields = preg_replace_callback('/enum\((.*?)\)/', function ($matches) {
            return 'enum(' . str_replace(',', '|', $matches[1]) . ')';
        }, $fields);

        $fieldsArray = explode(',', $fields);
        $fillable    = [];

        foreach ($fieldsArray as $field) {

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

        $relationsArray  = explode(',', $relations);
        $relationMethods = [];

        foreach ($relationsArray as $relation) {

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
