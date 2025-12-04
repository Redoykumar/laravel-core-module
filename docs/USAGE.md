# Usage Guide - Redoy CoreModule

Practical examples and patterns for using the Redoy CoreModule package in your Laravel application.

## Table of Contents

1. [Basic Setup](#basic-setup)
2. [Controllers](#controllers)
3. [Service Classes](#service-classes)
4. [Error Handling](#error-handling)
5. [Validation Responses](#validation-responses)
6. [Complex Data Responses](#complex-data-responses)
7. [RESTful Resource Endpoints](#restful-resource-endpoints)
8. [Authentication & Authorization](#authentication--authorization)

---

## Basic Setup

### 1. Add Trait to Controller

```php
<?php
namespace App\Http\Controllers;

use Redoy\CoreModule\Traits\ResponseHelperTrait;
use Redoy\CoreModule\Constants\ApiCodes;

class BaseController extends Controller
{
    use ResponseHelperTrait;
}
```

Then all your controllers can extend `BaseController`:

```php
class UserController extends BaseController
{
    // Now can use $this->successResponse() and $this->errorResponse()
}
```

---

## Controllers

### Simple GET Endpoint

```php
<?php
namespace App\Http\Controllers;

use Redoy\CoreModule\Traits\ResponseHelperTrait;
use Redoy\CoreModule\Constants\ApiCodes;
use App\Models\User;

class UserController extends Controller
{
    use ResponseHelperTrait;

    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return $this->errorResponse(
                ['id' => 'User not found'],
                ApiCodes::NOT_FOUND,
                'The requested user does not exist'
            );
        }
        
        return $this->successResponse(
            $user,
            ApiCodes::OK,
            'User retrieved successfully'
        );
    }
}
```

### POST Endpoint with Creation

```php
public function store(StoreUserRequest $request)
{
    try {
        $user = User::create($request->validated());
        
        return $this->successResponse(
            $user->load('profile'),  // eager load relations
            ApiCodes::CREATED,
            'User account created successfully'
        );
    } catch (\Exception $e) {
        return $this->errorResponse(
            ['error' => $e->getMessage()],
            ApiCodes::INTERNAL_SERVER_ERROR,
            'Failed to create user'
        );
    }
}
```

### DELETE Endpoint

```php
public function destroy($id)
{
    try {
        $user = User::findOrFail($id);
        $user->delete();
        
        return $this->successResponse(
            null,
            ApiCodes::NO_CONTENT,
            'User deleted successfully'
        );
    } catch (\ModelNotFoundException $e) {
        return $this->errorResponse(
            null,
            ApiCodes::NOT_FOUND,
            'User not found'
        );
    }
}
```

### PUT/PATCH Endpoint

```php
public function update(UpdateUserRequest $request, $id)
{
    $user = User::find($id);
    
    if (!$user) {
        return $this->errorResponse(
            null,
            ApiCodes::NOT_FOUND,
            'User not found'
        );
    }
    
    $user->update($request->validated());
    
    return $this->successResponse(
        $user,
        ApiCodes::OK,
        'User updated successfully'
    );
}
```

---

## Service Classes

Use the trait in service classes for consistent API responses:

```php
<?php
namespace App\Services;

use Redoy\CoreModule\Traits\ResponseHelperTrait;
use Redoy\CoreModule\Constants\ApiCodes;
use App\Models\Order;

class OrderService
{
    use ResponseHelperTrait;
    
    public function processOrder($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            if ($order->status !== 'pending') {
                return $this->errorResponse(
                    ['status' => $order->status],
                    ApiCodes::CONFLICT,
                    'Order cannot be processed in its current state'
                );
            }
            
            $order->update(['status' => 'processing']);
            
            // Perform actual processing...
            
            return $this->successResponse(
                $order,
                ApiCodes::ACCEPTED,
                'Order is being processed'
            );
        } catch (\ModelNotFoundException $e) {
            return $this->errorResponse(
                null,
                ApiCodes::NOT_FOUND,
                'Order not found'
            );
        }
    }
}
```

Usage in controller:

```php
class OrderController extends Controller
{
    use ResponseHelperTrait;
    
    public function __construct(private OrderService $service) {}
    
    public function process($id)
    {
        return $this->service->processOrder($id);
    }
}
```

---

## Error Handling

### Try-Catch Pattern

```php
public function riskyOperation()
{
    try {
        $data = $this->performComplexCalculation();
        return $this->successResponse($data);
    } catch (ValidationException $e) {
        return $this->errorResponse(
            $e->errors(),
            ApiCodes::UNPROCESSABLE_ENTITY,
            'Validation failed'
        );
    } catch (AuthorizationException $e) {
        return $this->errorResponse(
            null,
            ApiCodes::FORBIDDEN,
            'You do not have permission to perform this action'
        );
    } catch (\Exception $e) {
        \Log::error('Operation failed', ['error' => $e->getMessage()]);
        
        return $this->errorResponse(
            null,
            ApiCodes::INTERNAL_SERVER_ERROR,
            'An unexpected error occurred'
        );
    }
}
```

### Custom Exception Handling

```php
public function handle()
{
    try {
        // Business logic
    } catch (InsufficientFundsException $e) {
        return $this->errorResponse(
            ['balance' => $e->getCurrentBalance()],
            ApiCodes::PAYMENT_REQUIRED,
            'Insufficient funds'
        );
    } catch (RateLimitedException $e) {
        return $this->errorResponse(
            ['retry_after' => $e->getRetryAfterSeconds()],
            ApiCodes::TOO_MANY_REQUESTS,
            'Rate limit exceeded'
        );
    }
}
```

---

## Validation Responses

### With Laravel Validation

```php
public function store(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
        'name' => 'required|string|max:255',
    ]);
    
    if ($validator->fails()) {
        return $this->errorResponse(
            $validator->errors(),
            ApiCodes::UNPROCESSABLE_ENTITY,
            'Validation failed'
        );
    }
    
    $user = User::create($validator->validated());
    
    return $this->successResponse(
        $user,
        ApiCodes::CREATED,
        'User created successfully'
    );
}
```

### With Form Request

```php
public function store(StoreUserRequest $request)  // auto-validates
{
    $user = User::create($request->validated());
    
    return $this->successResponse(
        $user,
        ApiCodes::CREATED,
        'User created successfully'
    );
}

// In app/Http/Requests/StoreUserRequest.php
class StoreUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'name' => 'required|string|max:255',
        ];
    }
    
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            // Can inject ResponseHelperTrait here too
            response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Validation failed',
                'data' => $validator->errors(),
            ], 422)
        );
    }
}
```

---

## Complex Data Responses

### Nested Objects

```php
public function getUserWithRelations($userId)
{
    $user = User::with([
        'profile',
        'posts' => fn($q) => $q->limit(5),
        'comments' => fn($q) => $q->latest()->limit(10),
    ])->findOrFail($userId);
    
    return $this->successResponse(
        $user->append(['full_name', 'post_count']),  // Add accessors
        ApiCodes::OK,
        'User data retrieved'
    );
}
```

Response:
```json
{
  "success": true,
  "code": 200,
  "message": "User data retrieved",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "full_name": "John Doe",
    "post_count": 5,
    "profile": { "bio": "...", "avatar": "..." },
    "posts": [ { "id": 1, "title": "...", "body": "..." } ],
    "comments": [ { "id": 1, "text": "..." } ]
  }
}
```

### Collection with Metadata

```php
public function listUsers()
{
    $users = User::paginate(15);
    
    return $this->successResponse(
        [
            'users' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
        ],
        ApiCodes::OK,
        'Users list retrieved'
    );
}
```

### Search Results

```php
public function search(Request $request)
{
    $query = $request->query('q');
    $type = $request->query('type', 'all');  // users, posts, comments, all
    
    $results = [];
    
    if (in_array($type, ['users', 'all'])) {
        $results['users'] = User::where('name', 'like', "%$query%")->limit(5)->get();
    }
    
    if (in_array($type, ['posts', 'all'])) {
        $results['posts'] = Post::where('title', 'like', "%$query%")->limit(5)->get();
    }
    
    if (in_array($type, ['comments', 'all'])) {
        $results['comments'] = Comment::where('text', 'like', "%$query%")->limit(5)->get();
    }
    
    return $this->successResponse(
        $results,
        ApiCodes::OK,
        "Search results for '$query'"
    );
}
```

---

## RESTful Resource Endpoints

### Complete Resource Controller

```php
<?php
namespace App\Http\Controllers;

use Redoy\CoreModule\Traits\ResponseHelperTrait;
use Redoy\CoreModule\Constants\ApiCodes;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    use ResponseHelperTrait;

    // GET /posts
    public function index()
    {
        $posts = Post::with('author')->paginate(10);
        
        return $this->successResponse(
            $posts,
            ApiCodes::OK,
            'Posts retrieved'
        );
    }

    // POST /posts
    public function store(StorePostRequest $request)
    {
        $post = auth()->user()->posts()->create($request->validated());
        
        return $this->successResponse(
            $post->load('author'),
            ApiCodes::CREATED,
            'Post created successfully'
        );
    }

    // GET /posts/{post}
    public function show(Post $post)
    {
        return $this->successResponse(
            $post->load(['author', 'comments']),
            ApiCodes::OK,
            'Post retrieved'
        );
    }

    // PUT /posts/{post}
    public function update(UpdatePostRequest $request, Post $post)
    {
        if ($post->author_id !== auth()->id()) {
            return $this->errorResponse(
                null,
                ApiCodes::FORBIDDEN,
                'You cannot edit this post'
            );
        }
        
        $post->update($request->validated());
        
        return $this->successResponse(
            $post,
            ApiCodes::OK,
            'Post updated successfully'
        );
    }

    // DELETE /posts/{post}
    public function destroy(Post $post)
    {
        if ($post->author_id !== auth()->id()) {
            return $this->errorResponse(
                null,
                ApiCodes::FORBIDDEN,
                'You cannot delete this post'
            );
        }
        
        $post->delete();
        
        return $this->successResponse(
            null,
            ApiCodes::NO_CONTENT,
            'Post deleted successfully'
        );
    }
}
```

---

## Authentication & Authorization

### Token-based Response

```php
public function login(LoginRequest $request)
{
    if (!auth()->attempt($request->only('email', 'password'))) {
        return $this->errorResponse(
            null,
            ApiCodes::UNAUTHORIZED,
            'Invalid credentials'
        );
    }
    
    $user = auth()->user();
    $token = $user->createToken('api-token')->plainTextToken;
    
    return $this->successResponse(
        [
            'user' => $user,
            'token' => $token,
        ],
        ApiCodes::OK,
        'Login successful'
    );
}
```

### Authorization Check

```php
public function adminOnly()
{
    if (!auth()->user()->is_admin) {
        return $this->errorResponse(
            null,
            ApiCodes::FORBIDDEN,
            'Admin access required'
        );
    }
    
    return $this->successResponse(
        auth()->user(),
        ApiCodes::OK,
        'Admin user verified'
    );
}
```

### Permission-based Response

```php
public function publishPost(Post $post)
{
    if (!auth()->user()->can('publish', $post)) {
        return $this->errorResponse(
            null,
            ApiCodes::FORBIDDEN,
            'You do not have permission to publish this post'
        );
    }
    
    $post->publish();
    
    return $this->successResponse(
        $post,
        ApiCodes::OK,
        'Post published successfully'
    );
}
```

---

## Best Practices

1. **Always use ApiCodes constants** instead of magic numbers
2. **Provide meaningful messages** for client-side error handling
3. **Include relevant data** in error responses (field names, validation messages)
4. **Use appropriate HTTP status codes** (don't always return 200)
5. **Log errors** but don't expose sensitive details in responses
6. **Handle exceptions gracefully** with try-catch blocks
7. **Use the trait consistently** across all response-generating classes
8. **Test your responses** to ensure consistency

---

## Common Response Codes Quick Reference

| Scenario | Status Code | Method |
|----------|-------------|--------|
| Successful retrieval | 200 OK | `successResponse()` |
| Resource created | 201 CREATED | `successResponse()` |
| No content (deleted) | 204 NO_CONTENT | `successResponse()` |
| Validation errors | 422 UNPROCESSABLE_ENTITY | `errorResponse()` |
| Not found | 404 NOT_FOUND | `errorResponse()` |
| Unauthorized (no auth) | 401 UNAUTHORIZED | `errorResponse()` |
| Forbidden (no permission) | 403 FORBIDDEN | `errorResponse()` |
| Conflict (state issue) | 409 CONFLICT | `errorResponse()` |
| Server error | 500 INTERNAL_SERVER_ERROR | `errorResponse()` |

For a complete list of available codes, see [API Reference](API.md).
