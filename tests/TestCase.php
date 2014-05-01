<?php

declare(strict_types=1);

namespace PhilipRehberger\MakeService\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PhilipRehberger\MakeService\MakeServiceServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders(mixed $app): array
    {
        return [
            MakeServiceServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure generated files do not persist between tests
        $this->cleanupGeneratedFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanupGeneratedFiles();

        parent::tearDown();
    }

    protected function cleanupGeneratedFiles(): void
    {
        $paths = [
            app_path('Services'),
            app_path('DTOs'),
            app_path('Enums'),
            app_path('Actions'),
            app_path('ValueObjects'),
            app_path('Contracts'),
            base_path('tests/Unit/Services'),
            base_path('tests/Unit/DTOs'),
            base_path('tests/Unit/Enums'),
            base_path('tests/Unit/Actions'),
            base_path('tests/Unit/ValueObjects'),
            base_path('tests/Unit/Contracts'),
        ];

        foreach ($paths as $path) {
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            }
        }
    }

    protected function deleteDirectory(string $dir): void
    {
        $files = array_diff(scandir($dir) ?: [], ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
