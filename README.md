# Redoy CoreModule

A lightweight Laravel package that provides standardized JSON response handling and HTTP status code constants for modern API development.

## Features

- **ResponseHelperTrait**: Unified interface for success and error responses with consistent JSON formatting
- **ApiCodes**: Comprehensive HTTP status code constants (1xx, 2xx, 3xx, 4xx, 5xx)
- **JSON Response Builder Integration**: Built on top of the `marcin-orlowski/response-builder` package for flexible response construction
- **Type-safe Status Codes**: Named constants for all standard HTTP status codes (e.g., `ApiCodes::OK`, `ApiCodes::NOT_FOUND`)
- **Flexible Data & Messaging**: Support for custom data, error objects, and messages in responses

## Installation

1. **Via Composer** (when package is published):
   ```bash
   composer require redoy/core-module
   ```

2. **Local Development**:
   ```bash
   # Clone or link the package into packages/Redoy/CoreModule
   composer dump-autoload
   ```

3. **Verify Installation**:
   ```bash
   php artisan package:discover
   ```

## Quick Start

### Using ResponseHelperTrait

Add the trait to any class (Controller, Service, etc.):

```php
<?php
namespace App\Http\Controllers;

use Redoy\CoreModule\Traits\ResponseHelperTrait;
use Redoy\CoreModule\Constants\ApiCodes;

class UserController extends Controller
{
    use ResponseHelperTrait;

    public function index()
    {
        $users = User::all();
        return $this->successResponse($users, ApiCodes::OK, 'Users retrieved successfully');
    }

    public function store(Request $request)
    {
        $user = User::create($request->validated());
        return $this->successResponse($user, ApiCodes::CREATED, 'User created successfully');
    }

    public function destroy($id)
    {
        try {
            User::findOrFail($id)->delete();
            return $this->successResponse(null, ApiCodes::NO_CONTENT, 'User deleted');
        } catch (\Exception $e) {
            return $this->errorResponse(
                ['error' => 'User not found'],
                ApiCodes::NOT_FOUND,
                'Failed to delete user'
            );
        }
    }
}
```

### Using ApiCodes

Reference any HTTP status code via named constants:

```php
use Redoy\CoreModule\Constants\ApiCodes;

// 2xx Success codes
ApiCodes::OK;                      // 200
ApiCodes::CREATED;                 // 201
ApiCodes::ACCEPTED;                // 202
ApiCodes::NO_CONTENT;              // 204

// 4xx Client error codes
ApiCodes::BAD_REQUEST;             // 400
ApiCodes::UNAUTHORIZED;            // 401
ApiCodes::FORBIDDEN;               // 403
ApiCodes::NOT_FOUND;               // 404
ApiCodes::UNPROCESSABLE_ENTITY;    // 422

// 5xx Server error codes
ApiCodes::INTERNAL_SERVER_ERROR;   // 500
ApiCodes::SERVICE_UNAVAILABLE;     // 503

// View all codes as array
ApiCodes::HTTP_STATUS_CODES;       // Array of all status codes
```

## Project Structure

```
packages/Redoy/CoreModule/
├── src/
│   ├── Traits/
│   │   └── ResponseHelperTrait.php       # Main trait providing response methods
│   ├── Constants/
│   │   └── ApiCodes.php                  # HTTP status code constants
│   └── Providers/
│       └── CoreModuleServiceProvider.php # Service provider
├── tests/
│   ├── Unit/
│   │   ├── ResponseHelperTraitTest.php       # Original unit tests
│   │   └── JsonDrivenResponseHelperTest.php  # JSON-driven comprehensive tests
│   ├── Support/
│   │   └── TestResponseBuilder.php           # Test double for ResponseBuilder
│   ├── test_cases_clean.json                 # JSON test fixtures (20+ combinations)
│   ├── bootstrap.php                         # PHPUnit bootstrap
│   └── report/
│       ├── testdox.html                      # HTML test report
│       ├── junit.xml                         # JUnit XML test report
│       └── README.md                         # Report documentation
├── docs/
│   ├── API.md                            # API reference and method signatures
│   ├── USAGE.md                          # Practical usage examples
│   └── TESTING.md                        # Test suite documentation
├── composer.json
└── README.md                             # This file
```

## Requirements

- **PHP**: 8.2+
- **Laravel**: 11.0+
- **Dependencies**:
  - `marcin-orlowski/response-builder` (for JSON response building)
  - `illuminate/http` (for JsonResponse)

## Documentation

- [API Reference](docs/API.md) — Full method signatures, parameters, and return types
- [Usage Examples](docs/USAGE.md) — Practical code examples and patterns
- [Testing Guide](docs/TESTING.md) — How to run tests and understand test coverage
- [Test Reports](tests/report/README.md) — Automated test execution reports (HTML, JUnit XML)

## Testing

Run all tests:
```bash
./vendor/bin/phpunit packages/Redoy/CoreModule/tests
```

Run specific test file:
```bash
./vendor/bin/phpunit packages/Redoy/CoreModule/tests/Unit/ResponseHelperTraitTest.php
./vendor/bin/phpunit packages/Redoy/CoreModule/tests/Unit/JsonDrivenResponseHelperTest.php
```

Generate test reports:
```bash
./vendor/bin/phpunit \
  --log-junit packages/Redoy/CoreModule/tests/report/junit.xml \
  --testdox-html packages/Redoy/CoreModule/tests/report/testdox.html \
  packages/Redoy/CoreModule/tests
```

View test reports:
- **HTML Report**: Open `packages/Redoy/CoreModule/tests/report/testdox.html` in a browser
- **JUnit XML**: Available at `packages/Redoy/CoreModule/tests/report/junit.xml` for CI/CD integration

## Test Coverage

- **Unit Tests**: 34 tests covering ResponseHelperTrait methods, ApiCodes constants, and JSON encoding/decoding
- **JSON-Driven Tests**: 20 comprehensive test cases covering:
  - Valid success and error responses
  - Nested and complex data structures
  - Unicode and special characters
  - Edge cases (empty arrays, null values, mixed types)
  - Exception handling (invalid status codes, type errors)

**Overall**: 35 tests with 129 assertions, all passing ✓

## Common Response Patterns

### Success Response
```php
return $this->successResponse($data, ApiCodes::OK, 'Operation successful');
```

### Error Response
```php
return $this->errorResponse($errors, ApiCodes::BAD_REQUEST, 'Validation failed');
```

### No Content (204)
```php
return $this->successResponse(null, ApiCodes::NO_CONTENT, 'Deleted');
```

### Created Resource (201)
```php
return $this->successResponse($resource, ApiCodes::CREATED, 'Resource created');
```

## License

This package is part of the EnterpriseApp project.

## Contributing

1. Write tests for any new functionality (in `tests/Unit/`)
2. Ensure all tests pass: `./vendor/bin/phpunit packages/Redoy/CoreModule/tests`
3. Update documentation in `docs/` as needed
4. Regenerate test reports before committing

## Support

For issues, questions, or contributions, please refer to the project's main repository.
