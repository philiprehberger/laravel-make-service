<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService\Tests;

class ActionMakeCommandTest extends TestCase
{
    public function testItCreatesAnActionClass(): void
    {
        $this->artisan('make:action', ['name' => 'CreateUserAction'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Actions/CreateUserAction.php'));
    }

    public function testGeneratedActionHasCorrectNamespace(): void
    {
        $this->artisan('make:action', ['name' => 'CreateUserAction'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Actions/CreateUserAction.php'));

        $this->assertStringContainsString('namespace App\Actions;', $content);
        $this->assertStringContainsString('class CreateUserAction', $content);
    }

    public function testGeneratedActionHasInvokeMethod(): void
    {
        $this->artisan('make:action', ['name' => 'SendEmailAction'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Actions/SendEmailAction.php'));

        $this->assertStringContainsString('public function __invoke(', $content);
    }

    public function testGeneratedActionHasExecuteMethod(): void
    {
        $this->artisan('make:action', ['name' => 'ProcessPaymentAction'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Actions/ProcessPaymentAction.php'));

        $this->assertStringContainsString('public function execute(', $content);
    }

    public function testGeneratedActionInvokeCallsExecute(): void
    {
        $this->artisan('make:action', ['name' => 'ArchiveProjectAction'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Actions/ArchiveProjectAction.php'));

        $this->assertStringContainsString('return $this->execute(', $content);
    }

    public function testItCreatesActionInSubdirectory(): void
    {
        $this->artisan('make:action', ['name' => 'Auth/LogoutAction'])
            ->assertExitCode(0);

        $this->assertFileExists(app_path('Actions/Auth/LogoutAction.php'));

        $content = file_get_contents(app_path('Actions/Auth/LogoutAction.php'));

        $this->assertStringContainsString('namespace App\Actions\Auth;', $content);
    }

    public function testItGeneratesTestWhenFlagIsSet(): void
    {
        $this->artisan('make:action', [
            'name' => 'DeleteAccountAction',
            '--test' => true,
        ])->assertExitCode(0);

        $this->assertFileExists(app_path('Actions/DeleteAccountAction.php'));
        $this->assertFileExists(base_path('tests/Unit/Actions/DeleteAccountActionTest.php'));
    }

    public function testGeneratedTestHasCorrectContent(): void
    {
        $this->artisan('make:action', [
            'name' => 'PublishPostAction',
            '--test' => true,
        ])->assertExitCode(0);

        $content = file_get_contents(base_path('tests/Unit/Actions/PublishPostActionTest.php'));

        $this->assertStringContainsString('namespace Tests\Unit\Actions;', $content);
        $this->assertStringContainsString('class PublishPostActionTest', $content);
        $this->assertStringContainsString('test_it_is_invokable', $content);
    }

    public function testGeneratedActionHasStrictTypes(): void
    {
        $this->artisan('make:action', ['name' => 'StrictAction'])
            ->assertExitCode(0);

        $content = file_get_contents(app_path('Actions/StrictAction.php'));

        $this->assertStringContainsString('declare(strict_types=1);', $content);
    }

    public function testItDoesNotOverwriteExistingFileByDefault(): void
    {
        $this->artisan('make:action', ['name' => 'DuplicateAction'])
            ->assertExitCode(0);

        $originalContent = file_get_contents(app_path('Actions/DuplicateAction.php'));

        $this->artisan('make:action', ['name' => 'DuplicateAction'])
            ->assertExitCode(0);

        $this->assertSame($originalContent, file_get_contents(app_path('Actions/DuplicateAction.php')));
    }
}
