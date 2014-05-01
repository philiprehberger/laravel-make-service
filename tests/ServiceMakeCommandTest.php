<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService\Tests;

class ServiceMakeCommandTest extends TestCase
{
    public function testItCreatesAServiceClass(): void
    {
        $this->artisan('make:service', ['name' => 'UserService'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Services/UserService.php'));
    }

    public function testGeneratedServiceHasCorrectNamespace(): void
    {
        $this->artisan('make:service', ['name' => 'UserService'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Services/UserService.php'));

        $this->assertStringContainsString('namespace App\Services;', $content);
        $this->assertStringContainsString('class UserService', $content);
    }

    public function testItCreatesServiceInSubdirectory(): void
    {
        $this->artisan('make:service', ['name' => 'Auth/LoginService'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Services/Auth/LoginService.php'));
    }

    public function testSubdirectoryServiceHasCorrectNamespace(): void
    {
        $this->artisan('make:service', ['name' => 'Auth/LoginService'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Services/Auth/LoginService.php'));

        $this->assertStringContainsString('namespace App\Services\Auth;', $content);
        $this->assertStringContainsString('class LoginService', $content);
    }

    public function testItCreatesServiceWithModelInjection(): void
    {
        $this->artisan('make:service', [
            'name' => 'OrderService',
            '--model' => 'Order',
        ])->assertExitCode(0);

        $this->assertFileExists(app_path('Services/OrderService.php'));

        $content = file_get_contents(app_path('Services/OrderService.php'));

        $this->assertStringContainsString('App\Models\Order', $content);
        $this->assertStringContainsString('Order $order', $content);
    }

    public function testItGeneratesTestWhenFlagIsSet(): void
    {
        $this->artisan('make:service', [
            'name' => 'PaymentService',
            '--test' => true,
        ])->assertExitCode(0);

        $this->assertFileExists(app_path('Services/PaymentService.php'));
        $this->assertFileExists(base_path('tests/Unit/Services/PaymentServiceTest.php'));
    }

    public function testGeneratedTestHasCorrectContent(): void
    {
        $this->artisan('make:service', [
            'name' => 'InvoiceService',
            '--test' => true,
        ])->assertExitCode(0);

        $content = file_get_contents(base_path('tests/Unit/Services/InvoiceServiceTest.php'));

        $this->assertStringContainsString('namespace Tests\Unit\Services;', $content);
        $this->assertStringContainsString('class InvoiceServiceTest', $content);
        $this->assertStringContainsString('use App\Services\InvoiceService;', $content);
    }

    public function testItDoesNotOverwriteExistingFileByDefault(): void
    {
        $this->artisan('make:service', ['name' => 'DuplicateService'])
            ->assertExitCode(0);

        $originalContent = file_get_contents(app_path('Services/DuplicateService.php'));

        $this->artisan('make:service', ['name' => 'DuplicateService'])
            ->assertExitCode(0);

        $this->assertSame($originalContent, file_get_contents(app_path('Services/DuplicateService.php')));
    }

    public function testItOverwritesExistingFileWithForceFlag(): void
    {
        $this->artisan('make:service', ['name' => 'ForcedService'])
            ->assertExitCode(0);

        $this->artisan('make:service', [
            'name' => 'ForcedService',
            '--force' => true,
        ])->assertExitCode(0);

        $this->assertFileExists(app_path('Services/ForcedService.php'));
    }

    public function testGeneratedServiceHasConstructor(): void
    {
        $this->artisan('make:service', ['name' => 'ConstructorService'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Services/ConstructorService.php'));

        $this->assertStringContainsString('public function __construct()', $content);
    }

    public function testGeneratedServiceHasStrictTypes(): void
    {
        $this->artisan('make:service', ['name' => 'StrictService'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Services/StrictService.php'));

        $this->assertStringContainsString('declare(strict_types=1);', $content);
    }
}
