<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService\Tests;

class ContractMakeCommandTest extends TestCase
{
    public function testItCreatesAContractInterface(): void
    {
        $this->artisan('make:contract', ['name' => 'UserRepository'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Contracts/UserRepository.php'));
    }

    public function testGeneratedContractHasCorrectNamespace(): void
    {
        $this->artisan('make:contract', ['name' => 'UserRepository'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Contracts/UserRepository.php'));

        $this->assertStringContainsString('namespace App\Contracts;', $content);
        $this->assertStringContainsString('interface UserRepository', $content);
    }

    public function testGeneratedFileUsesInterfaceKeyword(): void
    {
        $this->artisan('make:contract', ['name' => 'PaymentGateway'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Contracts/PaymentGateway.php'));

        $this->assertStringContainsString('interface PaymentGateway', $content);
        $this->assertStringNotContainsString('class PaymentGateway', $content);
    }

    public function testItCreatesContractInSubdirectory(): void
    {
        $this->artisan('make:contract', ['name' => 'Repositories/ProjectRepository'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Contracts/Repositories/ProjectRepository.php'));

        $content = file_get_contents(app_path('Contracts/Repositories/ProjectRepository.php'));

        $this->assertStringContainsString('namespace App\Contracts\Repositories;', $content);
    }

    public function testItGeneratesTestWhenFlagIsSet(): void
    {
        $this->artisan('make:contract', [
            'name' => 'NotificationSender',
            '--test' => true,
        ])->assertExitCode(0);

        $this->assertFileExists(app_path('Contracts/NotificationSender.php'));
        $this->assertFileExists(base_path('tests/Unit/Contracts/NotificationSenderTest.php'));
    }

    public function testGeneratedTestHasCorrectContent(): void
    {
        $this->artisan('make:contract', [
            'name' => 'StorageDriver',
            '--test' => true,
        ])->assertExitCode(0);

        $content = file_get_contents(base_path('tests/Unit/Contracts/StorageDriverTest.php'));

        $this->assertStringContainsString('namespace Tests\Unit\Contracts;', $content);
        $this->assertStringContainsString('class StorageDriverTest', $content);
        $this->assertStringContainsString('test_it_is_an_interface', $content);
    }

    public function testGeneratedContractHasStrictTypes(): void
    {
        $this->artisan('make:contract', ['name' => 'StrictContract'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Contracts/StrictContract.php'));

        $this->assertStringContainsString('declare(strict_types=1);', $content);
    }

    public function testItDoesNotOverwriteExistingFileByDefault(): void
    {
        $this->artisan('make:contract', ['name' => 'DuplicateContract'])
            ->assertExitCode(0);

        $originalContent = file_get_contents(app_path('Contracts/DuplicateContract.php'));

        $this->artisan('make:contract', ['name' => 'DuplicateContract'])
            ->assertExitCode(0);

        $this->assertSame($originalContent, file_get_contents(app_path('Contracts/DuplicateContract.php')));
    }

    public function testItOverwritesExistingFileWithForceFlag(): void
    {
        $this->artisan('make:contract', ['name' => 'ForcedContract'])
            ->assertExitCode(0);

        $this->artisan('make:contract', [
            'name' => 'ForcedContract',
            '--force' => true,
        ])->assertExitCode(0);

        $this->assertFileExists(app_path('Contracts/ForcedContract.php'));
    }
}
