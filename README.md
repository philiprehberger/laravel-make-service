# Laravel Make Service

[![Tests](https://github.com/philiprehberger/laravel-make-service/actions/workflows/tests.yml/badge.svg)](https://github.com/philiprehberger/laravel-make-service/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/philiprehberger/laravel-make-service.svg)](https://packagist.org/packages/philiprehberger/laravel-make-service)
[![Last updated](https://img.shields.io/github/last-commit/philiprehberger/laravel-make-service)](https://github.com/philiprehberger/laravel-make-service/commits/main)

Artisan generator commands for services, DTOs, enums, actions, value objects, and interfaces.

## Requirements

- PHP 8.2+
- Laravel 11 or 12

## Installation

```bash
composer require philiprehberger/laravel-make-service
```

The package is auto-discovered by Laravel. No manual provider registration is needed.

### Publishing Stubs

Publish the stubs to customize the generated output:

```bash
php artisan vendor:publish --tag=make-service-stubs
```

Stubs are published to `stubs/make-service/` in your application root. Once published, the commands use your local stubs instead of the package defaults.

## Usage

All commands support the following shared flags:

| Flag | Description |
|------|-------------|
| `--test` / `-t` | Also generate a PHPUnit test file in `tests/Unit/` |
| `--force` / `-f` | Overwrite the file if it already exists |

### `make:service`

Generates a service class in `app/Services/`.

```bash
php artisan make:service UserService
```

**Generated file:** `app/Services/UserService.php`

```php
<?php

declare(strict_types=1);

namespace App\Services;

class UserService
{
    public function __construct()
    {
        //
    }
}
```

#### With model injection (`--model`)

```bash
php artisan make:service OrderService --model=Order
```

**Generated file:** `app/Services/OrderService.php`

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function __construct(
        protected Order $order,
    ) {
        //
    }
}
```

#### With test (`--test`)

```bash
php artisan make:service PaymentService --test
```

Generates both `app/Services/PaymentService.php` and `tests/Unit/Services/PaymentServiceTest.php`.

#### Subdirectories

```bash
php artisan make:service Auth/LoginService
```

Generates `app/Services/Auth/LoginService.php` with namespace `App\Services\Auth`.

### `make:dto`

Generates a readonly Data Transfer Object in `app/DTOs/`.

```bash
php artisan make:dto CreateUserDto
```

**Generated file:** `app/DTOs/CreateUserDto.php`

```php
<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class CreateUserDto
{
    public function __construct(
        //
    ) {}
}
```

Add constructor-promoted properties to define the shape of your DTO:

```php
readonly class CreateUserDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}
```

#### With test (`--test`)

```bash
php artisan make:dto CreateUserDto --test
```

Generates both `app/DTOs/CreateUserDto.php` and `tests/Unit/DTOs/CreateUserDtoTest.php`. The test asserts that the class is readonly and can be instantiated.

### `make:enum`

Generates a string-backed enum in `app/Enums/` with `values()`, `labels()`, and `fromName()` helper methods.

```bash
php artisan make:enum OrderStatus
```

**Generated file:** `app/Enums/OrderStatus.php`

```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case Example = 'example';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_reduce(self::cases(), function (array $carry, self $case): array {
            $carry[$case->value] = ucwords(str_replace(['_', '-'], ' ', $case->value));
            return $carry;
        }, []);
    }

    public static function fromName(string $name): self
    {
        foreach (self::cases() as $case) {
            if (strtolower($case->name) === strtolower($name)) {
                return $case;
            }
        }
        throw new \ValueError('"' . $name . '" is not a valid name for enum "' . self::class . '"');
    }
}
```

#### Integer-backed enum (`--int`)

```bash
php artisan make:enum Priority --int
```

Generates an `int`-backed enum with the same helper methods.

#### With test (`--test`)

```bash
php artisan make:enum OrderStatus --test
```

Generates both `app/Enums/OrderStatus.php` and `tests/Unit/Enums/OrderStatusTest.php`. The test covers `values()`, `labels()`, `fromName()`, and the invalid-name exception.

### `make:action`

Generates a single-action invokable class in `app/Actions/`. The `__invoke()` method delegates to `execute()`, allowing both direct invocation and explicit calling.

```bash
php artisan make:action CreateInvoiceAction
```

**Generated file:** `app/Actions/CreateInvoiceAction.php`

```php
<?php

declare(strict_types=1);

namespace App\Actions;

class CreateInvoiceAction
{
    public function __invoke(mixed ...$args): mixed
    {
        return $this->execute(...$args);
    }

    public function execute(mixed ...$args): mixed
    {
        //
    }
}
```

Typical usage after implementing the action:

```php
// Via dependency injection and invocation
$action = app(CreateInvoiceAction::class);
$invoice = $action($client, $lineItems);

// Or call execute directly
$invoice = $action->execute($client, $lineItems);
```

#### With test (`--test`)

```bash
php artisan make:action CreateInvoiceAction --test
```

Generates both `app/Actions/CreateInvoiceAction.php` and `tests/Unit/Actions/CreateInvoiceActionTest.php`. The test asserts invocability and that `__invoke` delegates to `execute`.

### `make:value`

Generates an immutable readonly value object in `app/ValueObjects/` with an `equals()` method.

```bash
php artisan make:value Money
```

**Generated file:** `app/ValueObjects/Money.php`

```php
<?php

declare(strict_types=1);

namespace App\ValueObjects;

readonly class Money
{
    public function __construct(
        //
    ) {}

    public function equals(self $other): bool
    {
        return $this == $other;
    }
}
```

Add constructor-promoted properties to define the value:

```php
readonly class Money
{
    public function __construct(
        public int $amount,
        public string $currency,
    ) {}

    public function equals(self $other): bool
    {
        return $this == $other;
    }
}
```

#### With test (`--test`)

```bash
php artisan make:value Money --test
```

Generates both `app/ValueObjects/Money.php` and `tests/Unit/ValueObjects/MoneyTest.php`. The test asserts the class is readonly and that `equals()` returns `true` for identical instances.

### `make:contract`

Generates a PHP interface in `app/Contracts/`.

```bash
php artisan make:contract PaymentGateway
```

**Generated file:** `app/Contracts/PaymentGateway.php`

```php
<?php

declare(strict_types=1);

namespace App\Contracts;

interface PaymentGateway
{
    //
}
```

Define your contract's public API and then bind it in a service provider:

```php
$this->app->bind(PaymentGateway::class, StripeGateway::class);
```

#### With test (`--test`)

```bash
php artisan make:contract PaymentGateway --test
```

Generates both `app/Contracts/PaymentGateway.php` and `tests/Unit/Contracts/PaymentGatewayTest.php`. The test asserts the generated file is a proper PHP interface.

### Customizing Stubs

After publishing stubs with `php artisan vendor:publish --tag=make-service-stubs`, edit any file in `stubs/make-service/`. The commands always prefer your published stubs over the package defaults.

Available stubs:

| File | Used by |
|------|---------|
| `stubs/make-service/service.stub` | `make:service` (no model) |
| `stubs/make-service/service.model.stub` | `make:service --model=` |
| `stubs/make-service/dto.stub` | `make:dto` |
| `stubs/make-service/enum.stub` | `make:enum` (string-backed) |
| `stubs/make-service/enum.int.stub` | `make:enum --int` |
| `stubs/make-service/action.stub` | `make:action` |
| `stubs/make-service/value.stub` | `make:value` |
| `stubs/make-service/contract.stub` | `make:contract` |
| `stubs/make-service/service.test.stub` | `--test` on `make:service` |
| `stubs/make-service/dto.test.stub` | `--test` on `make:dto` |
| `stubs/make-service/enum.test.stub` | `--test` on `make:enum` |
| `stubs/make-service/action.test.stub` | `--test` on `make:action` |
| `stubs/make-service/value.test.stub` | `--test` on `make:value` |
| `stubs/make-service/contract.test.stub` | `--test` on `make:contract` |

## API

| Command | Output location | Description |
|---------|----------------|-------------|
| `php artisan make:service {Name}` | `app/Services/` | Service class |
| `php artisan make:dto {Name}` | `app/DTOs/` | Readonly DTO |
| `php artisan make:enum {Name}` | `app/Enums/` | String-backed enum with helpers |
| `php artisan make:action {Name}` | `app/Actions/` | Invokable action class |
| `php artisan make:value {Name}` | `app/ValueObjects/` | Readonly value object |
| `php artisan make:contract {Name}` | `app/Contracts/` | PHP interface |

All commands accept `--test` / `-t` (generate a test file) and `--force` / `-f` (overwrite existing).

## Development

```bash
composer install
vendor/bin/phpunit
vendor/bin/pint --test
vendor/bin/phpstan analyse
```

## Support

If you find this project useful:

⭐ [Star the repo](https://github.com/philiprehberger/laravel-make-service)

🐛 [Report issues](https://github.com/philiprehberger/laravel-make-service/issues?q=is%3Aissue+is%3Aopen+label%3Abug)

💡 [Suggest features](https://github.com/philiprehberger/laravel-make-service/issues?q=is%3Aissue+is%3Aopen+label%3Aenhancement)

❤️ [Sponsor development](https://github.com/sponsors/philiprehberger)

🌐 [All Open Source Projects](https://philiprehberger.com/open-source-packages)

💻 [GitHub Profile](https://github.com/philiprehberger)

🔗 [LinkedIn Profile](https://www.linkedin.com/in/philiprehberger)

## License

[MIT](LICENSE)
