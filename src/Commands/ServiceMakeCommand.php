<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:service')]
class ServiceMakeCommand extends GeneratorCommand
{
    /** @var string */
    protected $name = 'make:service';

    /** @var string */
    protected $description = 'Create a new service class';

    /** @var string */
    protected $type = 'Service';

    protected function getStub(): string
    {
        $publishedStub = base_path('stubs/make-service/service.stub');

        if (file_exists($publishedStub)) {
            return $publishedStub;
        }

        if ($this->option('model')) {
            $modelStub = base_path('stubs/make-service/service.model.stub');
            if (file_exists($modelStub)) {
                return $modelStub;
            }

            return __DIR__ . '/../../stubs/service.model.stub';
        }

        return __DIR__ . '/../../stubs/service.stub';
    }

    protected function getDefaultNamespace(mixed $rootNamespace): string
    {
        return $rootNamespace . '\Services';
    }

    protected function buildClass(mixed $name): string
    {
        $stub = parent::buildClass($name);

        if ($model = $this->option('model')) {
            $stub = $this->replaceModel($stub, $model);
        }

        return $stub;
    }

    protected function replaceModel(string $stub, string $model): string
    {
        $modelClass = $this->parseModel($model);
        $modelVariable = lcfirst(class_basename($modelClass));

        $stub = str_replace('{{ modelImport }}', $modelClass, $stub);
        $stub = str_replace('{{ model }}', class_basename($modelClass), $stub);
        $stub = str_replace('{{ modelVariable }}', $modelVariable, $stub);

        return $stub;
    }

    protected function parseModel(string $model): string
    {
        if (preg_match('~[^\w\\\\]~', $model)) {
            throw new \InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The model that the service interacts with'],
            ['test', 't', InputOption::VALUE_NONE, 'Also generate a test file for the service'],
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite an existing file'],
        ];
    }

    public function handle(): bool|null
    {
        $result = parent::handle();

        if ($result !== false && $this->option('test')) {
            $this->generateTest();
        }

        return $result;
    }

    protected function generateTest(): void
    {
        $name = $this->qualifyClass($this->getNameInput());
        $testGenerator = new TestFileGenerator($this->laravel, $this->files);
        $testGenerator->generate($name, 'service', $this->output);
    }
}
