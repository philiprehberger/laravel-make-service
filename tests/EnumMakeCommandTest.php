<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService\Tests;

class EnumMakeCommandTest extends TestCase
{
    public function testItCreatesAStringBackedEnum(): void
    {
        $this->artisan('make:enum', ['name' => 'StatusEnum'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Enums/StatusEnum.php'));
    }

    public function testGeneratedEnumIsStringBackedByDefault(): void
    {
        $this->artisan('make:enum', ['name' => 'ColorEnum'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Enums/ColorEnum.php'));

        $this->assertStringContainsString('enum ColorEnum: string', $content);
    }

    public function testItCreatesAnIntBackedEnumWithFlag(): void
    {
        $this->artisan('make:enum', [
            'name' => 'PriorityEnum',
            '--int' => true,
        ])->assertExitCode(0);

        $this->assertFileExists(app_path('Enums/PriorityEnum.php'));

        $content = file_get_contents(app_path('Enums/PriorityEnum.php'));

        $this->assertStringContainsString('enum PriorityEnum: int', $content);
    }

    public function testGeneratedEnumHasValuesMethod(): void
    {
        $this->artisan('make:enum', ['name' => 'RoleEnum'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Enums/RoleEnum.php'));

        $this->assertStringContainsString('public static function values(): array', $content);
    }

    public function testGeneratedEnumHasLabelsMethod(): void
    {
        $this->artisan('make:enum', ['name' => 'TypeEnum'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Enums/TypeEnum.php'));

        $this->assertStringContainsString('public static function labels(): array', $content);
    }

    public function testGeneratedEnumHasFromNameMethod(): void
    {
        $this->artisan('make:enum', ['name' => 'ModeEnum'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Enums/ModeEnum.php'));

        $this->assertStringContainsString('public static function fromName(string $name): self', $content);
    }

    public function testGeneratedEnumHasCorrectNamespace(): void
    {
        $this->artisan('make:enum', ['name' => 'StateEnum'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Enums/StateEnum.php'));

        $this->assertStringContainsString('namespace App\Enums;', $content);
    }

    public function testItCreatesEnumInSubdirectory(): void
    {
        $this->artisan('make:enum', ['name' => 'Project/StatusEnum'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Enums/Project/StatusEnum.php'));

        $content = file_get_contents(app_path('Enums/Project/StatusEnum.php'));

        $this->assertStringContainsString('namespace App\Enums\Project;', $content);
    }

    public function testItGeneratesTestWhenFlagIsSet(): void
    {
        $this->artisan('make:enum', [
            'name' => 'VisibilityEnum',
            '--test' => true,
        ])->assertExitCode(0);

        $this->assertFileExists(app_path('Enums/VisibilityEnum.php'));
        $this->assertFileExists(base_path('tests/Unit/Enums/VisibilityEnumTest.php'));
    }

    public function testGeneratedTestCoversEnumMethods(): void
    {
        $this->artisan('make:enum', [
            'name' => 'ScopeEnum',
            '--test' => true,
        ])->assertExitCode(0);

        $content = file_get_contents(base_path('tests/Unit/Enums/ScopeEnumTest.php'));

        $this->assertStringContainsString('test_values_returns_array', $content);
        $this->assertStringContainsString('test_labels_returns_associative_array', $content);
        $this->assertStringContainsString('test_from_name_returns_correct_case', $content);
    }

    public function testGeneratedEnumHasStrictTypes(): void
    {
        $this->artisan('make:enum', ['name' => 'StrictEnum'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Enums/StrictEnum.php'));

        $this->assertStringContainsString('declare(strict_types=1);', $content);
    }

    public function testIntBackedEnumHasStrictTypes(): void
    {
        $this->artisan('make:enum', [
            'name' => 'IntStrictEnum',
            '--int' => true,
        ])->assertExitCode(0);

        $content = file_get_contents(app_path('Enums/IntStrictEnum.php'));

        $this->assertStringContainsString('declare(strict_types=1);', $content);
    }
}
