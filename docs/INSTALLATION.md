# Installation Guide - Redoy CoreModule

Complete installation and setup guide for the Redoy CoreModule package.

## Table of Contents

1. [Requirements](#requirements)
2. [Installation Methods](#installation-methods)
3. [Package Discovery](#package-discovery)
4. [Publishing Assets](#publishing-assets)
5. [Configuration](#configuration)
6. [Verification](#verification)
7. [Troubleshooting](#troubleshooting)

---

## Requirements

Before installing Redoy CoreModule, ensure your system meets these requirements:

- **PHP**: 8.2 or higher
- **Laravel**: 11.0 or higher (or Laravel 12.0+)
- **Composer**: Latest version

### Version Compatibility

| Package | Version |
|---------|---------|
| PHP | ^8.2 |
| Laravel | ^11.0 or ^12.0 |
| illuminate/http | ^11.0 or ^12.0 |
| marcin-orlowski/laravel-api-response-builder | ^12.1.1 |

---

## Installation Methods

### Method 1: Via Composer (Recommended)

Install the package using composer:

```bash
composer require redoy/core-module
```

**Expected output:**
```
Using version ^1.0 for redoy/core-module
Downloading redoy/core-module (v1.0.0)
```

### Method 2: Local Development

For local package development:

```bash
# Clone or link package to packages/Redoy/CoreModule
cd packages/Redoy/CoreModule

# Install dependencies
composer install

# Update autoloader
composer dump-autoload
```

### Method 3: With Specific Version

To install a specific version:

```bash
composer require redoy/core-module:1.0.0
```

---

## Package Discovery

Laravel automatically discovers service providers. However, you can manually trigger discovery:

```bash
php artisan package:discover
```

**What this does:**
- Registers `CoreModuleServiceProvider`
- Registers `CoreResponse` facade alias
- Makes all package functionality available

---

## Publishing Assets

After installation, publish package assets (configuration and language files):

### Publish All Assets

Publish all configuration files, language files, and other assets:

```bash
php artisan vendor:publish --provider="Redoy\CoreModule\Providers\CoreModuleServiceProvider"
```

**Published files:**
```
config/
  ├── core.php                          (package configuration)
  └── response_builder.php              (response builder config)

resources/lang/
  ├── en/
  │   └── api.php                       (English messages)
  └── bn/
      └── api.php                       (Bengali messages)
```

### Publish Only Configuration Files

To publish only configuration files:

```bash
php artisan vendor:publish --provider="Redoy\CoreModule\Providers\CoreModuleServiceProvider" --tag="config"
```

**Published files:**
- `config/core.php`
- `config/response_builder.php`

### Publish Only Language Files

To publish only language files:

```bash
php artisan vendor:publish --provider="Redoy\CoreModule\Providers\CoreModuleServiceProvider" --tag="lang"
```

**Published files:**
- `resources/lang/en/api.php`
- `resources/lang/bn/api.php`

### Force Republish

To overwrite existing published files:

```bash
php artisan vendor:publish --provider="Redoy\CoreModule\Providers\CoreModuleServiceProvider" --force
```

---

## Configuration

### Core Configuration

After publishing, edit `config/core.php` if needed:

```php
<?php

return [
    'namespace' => 'Redoy\CoreModule',
    'version' => '1.0.0',
];
```

### Response Builder Configuration

Edit `config/response_builder.php` for response builder settings:

```php
<?php

return [
    // Response builder configuration
    // Reference marcin-orlowski/response-builder documentation
];
```

### Language Configuration

The package includes English and Bengali language files in:
- `resources/lang/en/api.php`
- `resources/lang/bn/api.php`

Change your app's default language in `config/app.php`:

```php
'locale' => 'en',  // or 'bn' for Bengali
```

---

## Verification

Verify the installation was successful:

### Check Service Provider Registration

```bash
php artisan tinker
```

Then run:

```php
>>> app()->make('CoreResponse')
=> Redoy\CoreModule\Facades\CoreResponse
```

### Check Trait Availability

```php
>>> use Redoy\CoreModule\Traits\ResponseHelperTrait;
>>> use Redoy\CoreModule\Constants\ApiCodes;
```

### Run Tests

Run the package tests to verify everything works:

```bash
./vendor/bin/phpunit packages/Redoy/CoreModule/tests
```

**Expected output:**
```
OK (35 tests, 129 assertions)
```

---

## Complete Installation Workflow

Here's the complete step-by-step workflow:

```bash
# 1. Install package
composer require redoy/core-module

# 2. Auto-discover package
php artisan package:discover

# 3. Publish assets
php artisan vendor:publish --provider="Redoy\CoreModule\Providers\CoreModuleServiceProvider"

# 4. (Optional) Configure if needed
# Edit config/core.php if needed

# 5. Verify installation
php artisan tinker
# Then run: app()->make('CoreResponse')

# 6. (Optional) Run tests
./vendor/bin/phpunit packages/Redoy/CoreModule/tests

# 7. Start using the package!
```

---

## Troubleshooting

### Issue: "Class 'Redoy\CoreModule\Facades\CoreResponse' not found"

**Solution:**
```bash
# Clear application cache
php artisan cache:clear

# Clear compiled class cache
php artisan config:clear

# Re-discover packages
php artisan package:discover

# Clear autoloader
composer dump-autoload
```

### Issue: "Composer dependency conflict"

**Error message:**
```
don't install illuminate/http v11.47.0 (conflict with laravel/framework)
```

**Solution:**
This is normal. The package supports both Laravel 11 and 12:
- On Laravel 11: Uses standalone `illuminate/http`
- On Laravel 12: Uses built-in `illuminate/http` from `laravel/framework`

Composer will automatically resolve this.

### Issue: "Service provider not registered"

**Solution:**
```bash
# Force auto-discovery
php artisan package:discover --force

# Manually register in config/app.php if needed:
'providers' => [
    // ...
    Redoy\CoreModule\Providers\CoreModuleServiceProvider::class,
],
```

### Issue: "Configuration files not published"

**Solution:**
```bash
# Force republish all assets
php artisan vendor:publish --provider="Redoy\CoreModule\Providers\CoreModuleServiceProvider" --force

# Or republish just config
php artisan vendor:publish --provider="Redoy\CoreModule\Providers\CoreModuleServiceProvider" --tag="config" --force
```

### Issue: "PHP version too old"

**Error message:**
```
Package requires php ^8.2
```

**Solution:**
Upgrade PHP to version 8.2 or higher:
```bash
php --version  # Check current version
```

### Issue: "Laravel version incompatible"

**Error message:**
```
Package requires laravel ^11.0 || ^12.0
```

**Solution:**
Upgrade Laravel to version 11.0 or higher:
```bash
# For Laravel 11
composer require laravel/framework:^11.0

# For Laravel 12
composer require laravel/framework:^12.0
```

---

## After Installation

After successful installation, you can:

1. **Use the ResponseHelperTrait** in your controllers and services
2. **Use ApiCodes constants** for HTTP status codes
3. **Use CoreResponse facade** for convenient access
4. **Configure language files** for multilingual support
5. **Run tests** to verify everything works

See the [Usage Guide](USAGE.md) for practical examples.

---

## Additional Resources

- [README](../README.md) - Package overview
- [API Documentation](API.md) - Complete API reference
- [Usage Guide](USAGE.md) - Practical code examples
- [Testing Guide](TESTING.md) - How to run and write tests
- [GitHub Repository](https://github.com/Redoykumar/laravel-core-module)

---

**Version:** 1.0.0  
**Last Updated:** December 4, 2025
