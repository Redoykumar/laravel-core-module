# Changelog

All notable changes to the Redoy CoreModule will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-12-04

### Added
- Initial release of Redoy CoreModule
- `ResponseHelperTrait` with `successResponse()` and `errorResponse()` methods for standardized JSON API responses
- `ApiCodes` class with 100+ HTTP status code constants (1xx, 2xx, 3xx, 4xx, 5xx)
- Comprehensive API documentation (API.md, USAGE.md, TESTING.md)
- 34 unit tests for ResponseHelperTrait and ApiCodes constants
- 20 JSON-driven test cases covering edge cases and exception scenarios
- Automated test reports (HTML TestDox and JUnit XML)
- Main README with installation and quick start guide
- Support for Laravel 11.0+ and PHP 8.2+

### Features
- Unified interface for success and error responses
- Type-safe HTTP status code constants with meaningful names
- Built on marcin-orlowski/response-builder for flexible response construction
- Support for custom data, error messages, and metadata
- Comprehensive exception handling and validation error support
- 100% test coverage with passing tests (35 tests, 129 assertions)

### Documentation
- Complete API reference with method signatures and examples
- Practical usage guide with controller, service, and REST patterns
- Testing guide covering unit tests, JSON-driven tests, and report generation
- Contributing guidelines and best practices

---

## Release Notes

### Version 1.0.0 - Initial Release

This is the first stable release of the Redoy CoreModule package.

**Key Components:**
1. **ResponseHelperTrait** — Standardized response methods for controllers and services
2. **ApiCodes** — Centralized HTTP status code constants
3. **Comprehensive Testing** — 35 tests with 100% pass rate
4. **Full Documentation** — API reference, usage guide, and testing documentation

**Installation:**
```bash
composer require redoy/core-module
```

**Quick Start:**
```php
use Redoy\CoreModule\Traits\ResponseHelperTrait;
use Redoy\CoreModule\Constants\ApiCodes;

class MyController extends Controller
{
    use ResponseHelperTrait;
    
    public function handle()
    {
        return $this->successResponse($data, ApiCodes::OK, 'Success');
    }
}
```

**Testing:**
```bash
./vendor/bin/phpunit packages/Redoy/CoreModule/tests
```

**Reports:**
- HTML Report: `packages/Redoy/CoreModule/tests/report/testdox.html`
- JUnit XML: `packages/Redoy/CoreModule/tests/report/junit.xml`

---

## Planned Features (Future Releases)

- Response caching support
- Localization for response messages
- Response transformation pipelines
- GraphQL support
- Additional middleware for response handling
- Performance monitoring integration

---

## Support

For issues, feature requests, or contributions, please visit the [GitHub repository](https://github.com/redoy/core-module).

---

[1.0.0]: https://github.com/redoy/core-module/releases/tag/v1.0.0
