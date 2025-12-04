# API Reference - Redoy CoreModule

Complete API documentation for the Redoy CoreModule package.

## ResponseHelperTrait

A trait that provides standardized methods for generating JSON responses in Laravel applications.

### Location
`Redoy\CoreModule\Traits\ResponseHelperTrait`

### Methods

#### `successResponse()`

Generate a standardized success response.

**Signature:**
```php
public function successResponse(
    $data = null,
    ?int $api_code = null,
    ?string $message = null
): Illuminate\Http\JsonResponse
```

**Parameters:**
- `$data` (mixed, optional) — Response data (arrays, objects, primitives, or null)
- `$api_code` (int|null, optional) — HTTP status code. Defaults to `ApiCodes::OK` (200)
- `$message` (string|null, optional) — Response message

**Returns:**
- `Illuminate\Http\JsonResponse` — JSON response ready to send to client

**Examples:**
```php
// Simple success
return $this->successResponse($user);

// With custom message
return $this->successResponse($user, ApiCodes::OK, 'User found');

// No data (e.g., after deletion)
return $this->successResponse(null, ApiCodes::NO_CONTENT);

// With created status
return $this->successResponse($newUser, ApiCodes::CREATED, 'User created successfully');

// Complex data
return $this->successResponse([
    'user' => $user,
    'posts' => $user->posts,
    'stats' => ['total' => 42]
], ApiCodes::OK);
```

---

#### `errorResponse()`

Generate a standardized error response.

**Signature:**
```php
public function errorResponse(
    $data = null,
    ?int $api_code = null,
    ?string $message = null
): Illuminate\Http\JsonResponse
```

**Parameters:**
- `$data` (mixed, optional) — Error details (error messages, validation errors, etc.)
- `$api_code` (int|null, optional) — HTTP status code. Defaults to `ApiCodes::BAD_REQUEST` (400)
- `$message` (string|null, optional) — Error message

**Returns:**
- `Illuminate\Http\JsonResponse` — JSON error response

**Examples:**
```php
// Simple error
return $this->errorResponse(null, ApiCodes::NOT_FOUND, 'User not found');

// With error data
return $this->errorResponse(
    ['error' => 'Invalid credentials'],
    ApiCodes::UNAUTHORIZED,
    'Authentication failed'
);

// Validation errors
return $this->errorResponse(
    $validator->errors(),
    ApiCodes::UNPROCESSABLE_ENTITY,
    'Validation failed'
);

// Server error
return $this->errorResponse(
    ['exception' => $e->getMessage()],
    ApiCodes::INTERNAL_SERVER_ERROR,
    'Something went wrong'
);
```

---

## ApiCodes

Centralized HTTP status code constants for consistent API responses.

### Location
`Redoy\CoreModule\Constants\ApiCodes`

### Class Constants

#### 1xx Informational

```php
ApiCodes::CONTINUE                    // 100
ApiCodes::SWITCHING_PROTOCOLS         // 101
ApiCodes::PROCESSING                  // 102
ApiCodes::EARLY_HINTS                 // 103
```

#### 2xx Success

```php
ApiCodes::OK                          // 200 (default for success)
ApiCodes::CREATED                     // 201
ApiCodes::ACCEPTED                    // 202
ApiCodes::NON_AUTHORITATIVE_INFORMATION // 203
ApiCodes::NO_CONTENT                  // 204
ApiCodes::RESET_CONTENT               // 205
ApiCodes::PARTIAL_CONTENT             // 206
ApiCodes::MULTI_STATUS                // 207
ApiCodes::ALREADY_REPORTED            // 208
ApiCodes::IM_USED                     // 226
```

#### 3xx Redirection

```php
ApiCodes::MULTIPLE_CHOICES            // 300
ApiCodes::MOVED_PERMANENTLY           // 301
ApiCodes::FOUND                       // 302
ApiCodes::SEE_OTHER                   // 303
ApiCodes::NOT_MODIFIED                // 304
ApiCodes::USE_PROXY                   // 305
ApiCodes::SWITCH_PROXY                // 306
ApiCodes::TEMPORARY_REDIRECT          // 307
ApiCodes::PERMANENT_REDIRECT          // 308
```

#### 4xx Client Errors

```php
ApiCodes::BAD_REQUEST                 // 400 (default for errors)
ApiCodes::UNAUTHORIZED                // 401
ApiCodes::PAYMENT_REQUIRED            // 402
ApiCodes::FORBIDDEN                   // 403
ApiCodes::NOT_FOUND                   // 404
ApiCodes::METHOD_NOT_ALLOWED          // 405
ApiCodes::NOT_ACCEPTABLE              // 406
ApiCodes::PROXY_AUTHENTICATION_REQUIRED // 407
ApiCodes::REQUEST_TIMEOUT             // 408
ApiCodes::CONFLICT                    // 409
ApiCodes::GONE                        // 410
ApiCodes::LENGTH_REQUIRED             // 411
ApiCodes::PRECONDITION_FAILED         // 412
ApiCodes::PAYLOAD_TOO_LARGE           // 413
ApiCodes::URI_TOO_LONG                // 414
ApiCodes::UNSUPPORTED_MEDIA_TYPE      // 415
ApiCodes::RANGE_NOT_SATISFIABLE       // 416
ApiCodes::EXPECTATION_FAILED          // 417
ApiCodes::IM_A_TEAPOT                 // 418
ApiCodes::MISDIRECTED_REQUEST         // 421
ApiCodes::UNPROCESSABLE_ENTITY        // 422 (validation errors)
ApiCodes::LOCKED                      // 423
ApiCodes::FAILED_DEPENDENCY           // 424
ApiCodes::TOO_EARLY                   // 425
ApiCodes::UPGRADE_REQUIRED            // 426
ApiCodes::PRECONDITION_REQUIRED       // 428
ApiCodes::TOO_MANY_REQUESTS           // 429
ApiCodes::REQUEST_HEADER_FIELDS_TOO_LARGE // 431
ApiCodes::UNAVAILABLE_FOR_LEGAL_REASONS // 451
```

#### 5xx Server Errors

```php
ApiCodes::INTERNAL_SERVER_ERROR       // 500
ApiCodes::NOT_IMPLEMENTED             // 501
ApiCodes::BAD_GATEWAY                 // 502
ApiCodes::SERVICE_UNAVAILABLE         // 503
ApiCodes::GATEWAY_TIMEOUT             // 504
ApiCodes::HTTP_VERSION_NOT_SUPPORTED  // 505
ApiCodes::VARIANT_ALSO_NEGOTIATES     // 506
ApiCodes::INSUFFICIENT_STORAGE        // 507
ApiCodes::LOOP_DETECTED               // 508
ApiCodes::NOT_EXTENDED                // 510
ApiCodes::NETWORK_AUTHENTICATION_REQUIRED // 511
```

#### All Status Codes as Array

```php
ApiCodes::HTTP_STATUS_CODES  // Array of all 100+ status codes and their text representations
```

**Example Usage:**
```php
foreach (ApiCodes::HTTP_STATUS_CODES as $code => $text) {
    echo "$code => $text\n";  // "200 => OK"
}
```

---

## Response JSON Format

Responses generated by the trait conform to the response-builder library format:

### Success Response Example
```json
{
  "success": true,
  "code": 200,
  "locale": "en",
  "message": "Users retrieved successfully",
  "data": [
    { "id": 1, "name": "John Doe", "email": "john@example.com" }
  ]
}
```

### Error Response Example
```json
{
  "success": false,
  "code": 422,
  "locale": "en",
  "message": "Validation failed",
  "data": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

## Trait Usage

To use the trait in any class:

```php
<?php
namespace App\Http\Controllers;

use Redoy\CoreModule\Traits\ResponseHelperTrait;
use Redoy\CoreModule\Constants\ApiCodes;

class MyController extends Controller
{
    use ResponseHelperTrait;
    
    public function handle()
    {
        return $this->successResponse(
            data: ['result' => 'success'],
            api_code: ApiCodes::OK,
            message: 'Operation completed'
        );
    }
}
```

The trait is composable and works with any class (Controllers, Services, Jobs, etc.).

---

## Error Handling

When an invalid HTTP status code is used, a `Symfony\Component\HttpFoundation\Exception\InvalidArgumentException` is thrown by the underlying Symfony HttpFoundation library:

```php
try {
    // This will throw InvalidArgumentException (999 is not a valid HTTP code)
    return $this->successResponse($data, 999);
} catch (\InvalidArgumentException $e) {
    // Handle invalid status code
    return $this->errorResponse(
        ['error' => 'Internal server configuration error'],
        ApiCodes::INTERNAL_SERVER_ERROR
    );
}
```

**Valid HTTP Status Codes**: 100–599 (standard range)

---

## Type Safety

Both methods accept flexible parameter types:

- `$data`: Any JSON-serializable type (array, object, string, number, boolean, null)
- `$api_code`: Integer (or null to use default)
- `$message`: String (or null to omit)

Type validation is performed by the underlying response-builder and Symfony HttpFoundation.
