<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:enum')]
class EnumMakeCommand extends GeneratorCommand
{
    /** @var string */
    protected $name = 'make:enum';

    /** @var string */
    protected $description = 'Create a new enum class';

    /** @var string */
    protected $type = 'Enum';

    protected function getStub(): string
    {
        if ($this->option('int')) {
            $publishedStub = base_path('stubs/make-service/enum.int.stub');
            if (file_exists($publishedStub)) {
                return $publishedStub;
            }

            return __DIR__ . '/../../stubs/enum.int.stub';
        }

        $publishedStub = base_path('stubs/make-service/enum.stub');

        if (file_exists($publishedStub)) {
            return $publishedStub;
        }

        return __DIR__ . '/../../stubs/enum.stub';
    }

    protected function getDefaultNamespace(mixed $rootNamespace): string
    {
        return $rootNamespace . '\Enums';
    }

    protected function getOptions(): array
    {
        return [
            ['int', null, InputOption::VALUE_NONE, 'Create an integer-backed enum instead of string-backed'],
            ['test', 't', InputOption::VALUE_NONE, 'Also generate a test file for the enum'],
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
        $testGenerator->generate($name, 'enum', $this->output);
    }
}
