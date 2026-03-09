<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService;

use Illuminate\Foundation\Console\EnumMakeCommand as LaravelEnumMakeCommand;
use Illuminate\Support\ServiceProvider;
use PhilipRehberger\MakeService\Commands\ActionMakeCommand;
use PhilipRehberger\MakeService\Commands\ContractMakeCommand;
use PhilipRehberger\MakeService\Commands\DtoMakeCommand;
use PhilipRehberger\MakeService\Commands\EnumMakeCommand;
use PhilipRehberger\MakeService\Commands\ServiceMakeCommand;
use PhilipRehberger\MakeService\Commands\ValueMakeCommand;

class MakeServiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Override Laravel's native make:enum binding so our enhanced version
        // (which adds --int flag, helper methods, and --test support) takes
        // precedence in the IoC container.
        $this->app->extend(LaravelEnumMakeCommand::class, function () {
            return new EnumMakeCommand($this->app['files']);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ServiceMakeCommand::class,
                DtoMakeCommand::class,
                EnumMakeCommand::class,
                ActionMakeCommand::class,
                ValueMakeCommand::class,
                ContractMakeCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../stubs' => base_path('stubs/make-service'),
            ], 'make-service-stubs');
        }
    }
}
