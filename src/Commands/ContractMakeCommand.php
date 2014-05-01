<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:contract')]
class ContractMakeCommand extends GeneratorCommand
{
    /** @var string */
    protected $name = 'make:contract';

    /** @var string */
    protected $description = 'Create a new interface (contract)';

    /** @var string */
    protected $type = 'Contract';

    protected function getStub(): string
    {
        $publishedStub = base_path('stubs/make-service/contract.stub');

        if (file_exists($publishedStub)) {
            return $publishedStub;
        }

        return __DIR__ . '/../../stubs/contract.stub';
    }

    protected function getDefaultNamespace(mixed $rootNamespace): string
    {
        return $rootNamespace . '\Contracts';
    }

    protected function getOptions(): array
    {
        return [
            ['test', 't', InputOption::VALUE_NONE, 'Also generate a test file for the contract'],
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
        $testGenerator->generate($name, 'contract', $this->output);
    }
}
