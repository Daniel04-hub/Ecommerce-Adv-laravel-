# Swagger API Documentation Guide

## Overview
L5-Swagger has been successfully installed to provide API documentation for mobile apps, vendor integrations, and admin services.

## Access Control

**Swagger UI URL:** `/api/documentation`

**Authorization:** Admin users only
- Only users with the `admin` role can access the Swagger UI
- Configured via middleware in `config/l5-swagger.php`
- Unauthorized users will receive a 403 Forbidden response

## Installation Summary

### Packages Installed
- **darkaonline/l5-swagger** v9.0.1
- **zircote/swagger-php** v5.7.6
- **swagger-api/swagger-ui** v5.31.0
- OpenAPI 3.0.0 specification

### Files Created/Modified
- `config/l5-swagger.php` - Swagger configuration
- `app/Http/Controllers/Controller.php` - Base API annotations
- `storage/api-docs/api-docs.json` - Generated API documentation
- Annotations added to controllers (no logic changes)

## Documented APIs

### 1. COD Verification APIs
**Tag:** `COD Verification`

#### POST `/orders/{order}/cod/generate-otp`
- **Purpose:** Customer generates OTP for delivery verification
- **Auth:** Required (Sanctum token)
- **Response:** OTP sent to customer email/phone

#### POST `/cod/verify`
- **Purpose:** Delivery person verifies OTP to confirm COD payment
- **Auth:** Not required (public endpoint for delivery persons)
- **Body:**
  ```json
  {
    "order_id": 1,
    "otp": "123456"
  }
  ```

#### GET `/orders/{order}/cod/status`
- **Purpose:** Check if OTP is still active
- **Auth:** Required (Sanctum token)
- **Response:** OTP expiry time and attempt count

### 2. OTP Authentication APIs
**Tag:** `OTP Authentication`

#### POST `/login/otp/resend`
- **Purpose:** Resend OTP for customer login
- **Auth:** Not required (guest endpoint)
- **Body:**
  ```json
  {
    "email": "customer@example.com"
  }
  ```

## Usage Guide

### Accessing Swagger UI

1. **Login as Admin:**
   ```
   https://yourdomain.com/login
   ```
   Use admin credentials

2. **Navigate to Swagger UI:**
   ```
   https://yourdomain.com/api/documentation
   ```

3. **Explore APIs:**
   - Browse available endpoints by tag
   - View request/response schemas
   - Test APIs directly in the UI

### Testing APIs in Swagger UI

1. **Click on an endpoint** to expand details
2. **Click "Try it out"** button
3. **Fill in parameters** (path, query, body)
4. **Add authorization** if required:
   - Click "Authorize" button at top
   - Enter Bearer token from Sanctum
5. **Click "Execute"** to send request
6. **View response** below

## Authentication

### Sanctum Bearer Token

Most authenticated endpoints require a Bearer token:

```http
Authorization: Bearer {your-sanctum-token}
```

**To obtain a token:**
1. Login via standard auth endpoints
2. Token is returned in login response
3. Copy token for API requests

**In Swagger UI:**
1. Click "Authorize" button (lock icon)
2. Enter: `Bearer {token}`
3. Click "Authorize"
4. Now all requests will include the token

## Adding New API Documentation

### 1. Add Annotations to Controller Methods

```php
/**
 * @OA\Post(
 *     path="/api/your-endpoint",
 *     tags={"Your Tag"},
 *     summary="Brief description",
 *     description="Detailed description",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"field1"},
 *             @OA\Property(property="field1", type="string", example="value")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success response",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true)
 *         )
 *     )
 * )
 */
public function yourMethod(Request $request)
{
    // Your existing code (unchanged)
}
```

### 2. Regenerate Documentation

```bash
php artisan l5-swagger:generate
```

### 3. Refresh Swagger UI

Clear browser cache or force refresh (Ctrl+F5)

## Common Annotation Examples

### GET Endpoint with Path Parameter

```php
/**
 * @OA\Get(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Get user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Success")
 * )
 */
```

### POST with Authentication

```php
/**
 * @OA\Post(
 *     path="/api/orders",
 *     tags={"Orders"},
 *     summary="Create order",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="product_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Order created")
 * )
 */
```

### Query Parameters

```php
/**
 * @OA\Get(
 *     path="/api/products",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer", default=1)
 *     ),
 *     @OA\Response(response=200, description="Products list")
 * )
 */
```

## Configuration

### Environment Variables

Add to `.env` for customization:

```env
# Swagger UI path (default: api/documentation)
L5_SWAGGER_CONST_HOST=https://yourdomain.com

# Use absolute path for assets
L5_SWAGGER_USE_ABSOLUTE_PATH=true

# OpenAPI version
L5_SWAGGER_OPEN_API_SPEC_VERSION=3.0.0
```

### Admin-Only Access

Configured in `config/l5-swagger.php`:

```php
'middleware' => [
    'api' => ['web', 'auth', 'role:admin'],
    'docs' => ['web', 'auth', 'role:admin'],
],
```

### Scan Directories

By default, L5-Swagger scans the entire `app/` directory:

```php
'annotations' => [
    base_path('app'),
],
```

To limit scanning (improves performance):

```php
'annotations' => [
    base_path('app/Http/Controllers'),
],
```

## Best Practices

### 1. Keep Annotations Updated
- Update annotations when you modify API logic
- Document all request/response fields
- Include example values

### 2. Use Consistent Tags
- Group related endpoints under same tag
- Use clear, descriptive tag names
- Example: "Order Management", "User Authentication"

### 3. Document All Responses
- Include success responses (200, 201, etc.)
- Include error responses (400, 401, 403, 404, 500)
- Add descriptions for each response

### 4. Provide Examples
- Add `example` property to parameters
- Use realistic example values
- Help developers understand expected format

### 5. Security Documentation
- Mark authenticated endpoints with `security`
- Document required permissions
- Explain token requirements

## Troubleshooting

### Swagger UI Returns 403
- Ensure you're logged in as admin
- Check user has 'admin' role:
  ```php
  User::find(1)->hasRole('admin')
  ```

### Documentation Not Updating
1. Regenerate docs:
   ```bash
   php artisan l5-swagger:generate
   ```
2. Clear config cache:
   ```bash
   php artisan config:clear
   ```
3. Hard refresh browser (Ctrl+F5)

### Annotations Not Detected
- Check PHP syntax in annotations
- Ensure controller is in scanned directory
- Verify namespace is correct
- Check for typos in annotation names

### "Unable to render this definition"
- Validate JSON structure
- Check for missing required fields
- Ensure OpenAPI spec compliance

## Production Recommendations

### 1. Disable in Production (Optional)

If you don't want public API docs in production:

```php
// config/l5-swagger.php
'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),
```

```env
# .env (production)
L5_SWAGGER_GENERATE_ALWAYS=false
```

### 2. Cache Documentation

Generate once and commit to version control:

```bash
php artisan l5-swagger:generate
git add storage/api-docs/api-docs.json
```

### 3. Restrict Access

Keep admin-only middleware in production:
- Prevents unauthorized API discovery
- Protects internal endpoints
- Maintains security posture

## Alternative: API Documentation for Partners

If you need public API docs for vendors/partners:

1. **Create separate documentation:**
   ```php
   // config/l5-swagger.php
   'documentations' => [
       'public' => [
           'api' => ['title' => 'Public API'],
           'routes' => ['api' => 'api/docs/public'],
           'middleware' => ['api' => []], // No auth
       ],
   ],
   ```

2. **Use different annotations:**
   ```php
   /**
    * @OA\OpenApi(
    *     @OA\Info(title="Public API", version="1.0")
    * )
    */
   ```

## Security Notes

- ✅ Admin-only access configured
- ✅ No changes to existing API logic
- ✅ Annotations are comments (no runtime impact)
- ✅ Private endpoints not exposed
- ✅ Authentication requirements documented

## Next Steps

1. **Add more annotations** to other API controllers
2. **Document vendor APIs** for marketplace integration
3. **Add response schemas** for complex objects
4. **Create API usage examples** for mobile app developers
5. **Set up API versioning** if needed

---

**Installation Date:** December 23, 2025  
**Laravel Version:** 12.x  
**L5-Swagger Version:** 9.0.1  
**OpenAPI Version:** 3.0.0
