<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService\Tests;

class DtoMakeCommandTest extends TestCase
{
    public function testItCreatesADtoClass(): void
    {
        $this->artisan('make:dto', ['name' => 'UserDto'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('DTOs/UserDto.php'));
    }

    public function testGeneratedDtoHasCorrectNamespace(): void
    {
        $this->artisan('make:dto', ['name' => 'UserDto'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('DTOs/UserDto.php'));

        $this->assertStringContainsString('namespace App\DTOs;', $content);
        $this->assertStringContainsString('class UserDto', $content);
    }

    public function testGeneratedDtoIsReadonly(): void
    {
        $this->artisan('make:dto', ['name' => 'ProductDto'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('DTOs/ProductDto.php'));

        $this->assertStringContainsString('readonly class ProductDto', $content);
    }

    public function testGeneratedDtoHasConstructor(): void
    {
        $this->artisan('make:dto', ['name' => 'OrderDto'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('DTOs/OrderDto.php'));

        $this->assertStringContainsString('public function __construct(', $content);
    }

    public function testItCreatesDtoInSubdirectory(): void
    {
        $this->artisan('make:dto', ['name' => 'Auth/LoginDto'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('DTOs/Auth/LoginDto.php'));

        $content = file_get_contents(app_path('DTOs/Auth/LoginDto.php'));

        $this->assertStringContainsString('namespace App\DTOs\Auth;', $content);
    }

    public function testItGeneratesTestWhenFlagIsSet(): void
    {
        $this->artisan('make:dto', [
            'name' => 'PaymentDto',
            '--test' => true,
        ])->assertExitCode(0);

        $this->assertFileExists(app_path('DTOs/PaymentDto.php'));
        $this->assertFileExists(base_path('tests/Unit/DTOs/PaymentDtoTest.php'));
    }

    public function testGeneratedTestHasCorrectContent(): void
    {
        $this->artisan('make:dto', [
            'name' => 'InvoiceDto',
            '--test' => true,
        ])->assertExitCode(0);

        $content = file_get_contents(base_path('tests/Unit/DTOs/InvoiceDtoTest.php'));

        $this->assertStringContainsString('namespace Tests\Unit\DTOs;', $content);
        $this->assertStringContainsString('class InvoiceDtoTest', $content);
        $this->assertStringContainsString('use App\DTOs\InvoiceDto;', $content);
        $this->assertStringContainsString('isReadOnly', $content);
    }

    public function testGeneratedDtoHasStrictTypes(): void
    {
        $this->artisan('make:dto', ['name' => 'StrictDto'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('DTOs/StrictDto.php'));

        $this->assertStringContainsString('declare(strict_types=1);', $content);
    }

    public function testItDoesNotOverwriteExistingFileByDefault(): void
    {
        $this->artisan('make:dto', ['name' => 'DuplicateDto'])
            ->assertExitCode(0);

        $originalContent = file_get_contents(app_path('DTOs/DuplicateDto.php'));

        $this->artisan('make:dto', ['name' => 'DuplicateDto'])
            ->assertExitCode(0);

        $this->assertSame($originalContent, file_get_contents(app_path('DTOs/DuplicateDto.php')));
    }
}
