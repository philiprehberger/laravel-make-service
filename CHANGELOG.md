# Changelog

All notable changes to this package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.5] - 2026-03-31

### Changed
- Standardize README to 3-badge format with emoji Support section
- Update CI checkout action to v5 for Node.js 24 compatibility
- Add GitHub issue templates, dependabot config, and PR template

## [1.0.4] - 2026-03-21

### Changed
- Consolidate README and configuration updates from diverged branch

## [1.0.2] - 2026-03-17

### Changed
- Standardized package metadata, README structure, and CI workflow per package guide

## [1.0.1] - 2026-03-16

### Changed
- Standardize composer.json: add homepage, scripts
- Add Development section to README

## [1.0.0] - 2026-03-09

### Added

- `make:service` command — generates a service class with constructor; accepts `--model=` to inject an Eloquent model.
- `make:dto` command — generates a readonly class with public constructor promotion.
- `make:enum` command — generates a string-backed enum with `values()`, `labels()`, and `fromName()` helper methods; accepts `--int` for integer-backed enums.
- `make:action` command — generates a single-action invokable class with `__invoke()` delegating to `execute()`.
- `make:value` command — generates an immutable readonly value object with an `equals()` method.
- `make:contract` command — generates a PHP interface.
- `--test` flag available on every command to co-generate a matching PHPUnit test file.
- `--force` flag available on every command to overwrite existing files.
- Stub publishing via `php artisan vendor:publish --tag=make-service-stubs`.
- Full PHPStan level 8 compliance.
- PSR-12 code style via Laravel Pint.
- GitHub Actions CI matrix for PHP 8.2, 8.3, 8.4 against Laravel 11 and 12.

[Unreleased]: https://github.com/philiprehberger/laravel-make-service/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/philiprehberger/laravel-make-service/releases/tag/v1.0.0
