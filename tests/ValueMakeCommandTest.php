<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService\Tests;

class ValueMakeCommandTest extends TestCase
{
    public function testItCreatesAValueObjectClass(): void
    {
        $this->artisan('make:value', ['name' => 'Money'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('ValueObjects/Money.php'));
    }

    public function testGeneratedValueObjectHasCorrectNamespace(): void
    {
        $this->artisan('make:value', ['name' => 'Money'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('ValueObjects/Money.php'));

        $this->assertStringContainsString('namespace App\ValueObjects;', $content);
        $this->assertStringContainsString('class Money', $content);
    }

    public function testGeneratedValueObjectIsReadonly(): void
    {
        $this->artisan('make:value', ['name' => 'Email'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('ValueObjects/Email.php'));

        $this->assertStringContainsString('readonly class Email', $content);
    }

    public function testGeneratedValueObjectHasEqualsMethod(): void
    {
        $this->artisan('make:value', ['name' => 'Uuid'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('ValueObjects/Uuid.php'));

        $this->assertStringContainsString('public function equals(self $other): bool', $content);
    }

    public function testGeneratedValueObjectHasConstructor(): void
    {
        $this->artisan('make:value', ['name' => 'PhoneNumber'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('ValueObjects/PhoneNumber.php'));

        $this->assertStringContainsString('public function __construct(', $content);
    }

    public function testItCreatesValueObjectInSubdirectory(): void
    {
        $this->artisan('make:value', ['name' => 'Finance/Currency'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('ValueObjects/Finance/Currency.php'));

        $content = file_get_contents(app_path('ValueObjects/Finance/Currency.php'));

        $this->assertStringContainsString('namespace App\ValueObjects\Finance;', $content);
    }

    public function testItGeneratesTestWhenFlagIsSet(): void
    {
        $this->artisan('make:value', [
            'name' => 'Coordinate',
            '--test' => true,
        ])->assertExitCode(0);

        $this->assertFileExists(app_path('ValueObjects/Coordinate.php'));
        $this->assertFileExists(base_path('tests/Unit/ValueObjects/CoordinateTest.php'));
    }

    public function testGeneratedTestHasCorrectContent(): void
    {
        $this->artisan('make:value', [
            'name' => 'Temperature',
            '--test' => true,
        ])->assertExitCode(0);

        $content = file_get_contents(base_path('tests/Unit/ValueObjects/TemperatureTest.php'));

        $this->assertStringContainsString('namespace Tests\Unit\ValueObjects;', $content);
        $this->assertStringContainsString('class TemperatureTest', $content);
        $this->assertStringContainsString('test_it_is_readonly', $content);
        $this->assertStringContainsString('test_equals_returns_true_for_identical_instances', $content);
    }

    public function testGeneratedValueObjectHasStrictTypes(): void
    {
        $this->artisan('make:value', ['name' => 'StrictValue'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('ValueObjects/StrictValue.php'));

        $this->assertStringContainsString('declare(strict_types=1);', $content);
    }

    public function testItDoesNotOverwriteExistingFileByDefault(): void
    {
        $this->artisan('make:value', ['name' => 'DuplicateValue'])
            ->assertExitCode(0);

        $originalContent = file_get_contents(app_path('ValueObjects/DuplicateValue.php'));

        $this->artisan('make:value', ['name' => 'DuplicateValue'])
            ->assertExitCode(0);

        $this->assertSame($originalContent, file_get_contents(app_path('ValueObjects/DuplicateValue.php')));
    }
}
