<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService\Commands;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

class TestFileGenerator
{
    public function __construct(
        protected Application $app,
        protected Filesystem $files,
    ) {
    }

    public function generate(string $qualifiedClassName, string $type, OutputInterface $output): void
    {
        $stubPath = $this->resolveStub($type);
        $stub = $this->files->get($stubPath);

        $namespace = $this->resolveTestNamespace($qualifiedClassName);
        $className = class_basename($qualifiedClassName) . 'Test';
        $sourceClass = $qualifiedClassName;

        $stub = str_replace('{{ namespace }}', $namespace, $stub);
        $stub = str_replace('{{ class }}', $className, $stub);
        $stub = str_replace('{{ sourceClass }}', $sourceClass, $stub);
        $stub = str_replace('{{ sourceClassName }}', class_basename($sourceClass), $stub);

        $testPath = $this->resolveTestPath($qualifiedClassName);

        $directory = dirname($testPath);
        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        if ($this->files->exists($testPath)) {
            $output->writeln('<comment>Test already exists:</comment> ' . $testPath);

            return;
        }

        $this->files->put($testPath, $stub);

        $output->writeln('<info>Test created successfully:</info> ' . $testPath);
    }

    protected function resolveStub(string $type): string
    {
        $publishedStub = base_path("stubs/make-service/{$type}.test.stub");

        if (file_exists($publishedStub)) {
            return $publishedStub;
        }

        return __DIR__ . "/../../stubs/{$type}.test.stub";
    }

    protected function resolveTestNamespace(string $qualifiedClassName): string
    {
        $appNamespace = $this->app->getNamespace();
        $relative = str_replace($appNamespace, '', $qualifiedClassName);
        $parts = explode('\\', trim($relative, '\\'));
        array_pop($parts);

        if (empty($parts)) {
            return 'Tests\\Unit';
        }

        return 'Tests\\Unit\\' . implode('\\', $parts);
    }

    protected function resolveTestPath(string $qualifiedClassName): string
    {
        $appNamespace = $this->app->getNamespace();
        $relative = str_replace($appNamespace, '', $qualifiedClassName);
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative);

        return base_path('tests/Unit/' . $relative . 'Test.php');
    }
}
