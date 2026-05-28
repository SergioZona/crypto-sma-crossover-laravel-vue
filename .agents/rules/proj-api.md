# API Conventions

## URL Structure

```
/health          → liveness and connectivity probe (no version prefix)
/api/v1/crossovers/calculate → calculated crossover endpoint (POST)
/api/v1/crossovers/history   → historic queries endpoint (GET)
```

## HTTP Verbs

| Verb | Use |
|---|---|
| GET | Retrieve data (e.g. calculation history) |
| POST | Execute calculations / trigger actions |

## JSend Response Format

**Always use standard success/fail/error JSON layout in Controllers.**

```php
// Success (200 OK)
return response()->json([
    'status' => 'success',
    'data' => $result
]);

// Fail (400 Bad Request, 401 Unauthorized)
return response()->json([
    'status' => 'fail',
    'data' => $errors
], 400);

// Error (500 Internal Server Error)
return response()->json([
    'status' => 'error',
    'message' => $errorMessage,
    'code' => 500
], 500);
```

## Status Codes

| Code | When |
|---|---|
| 200 | Successful calculation query / history fetch |
| 400 | Param validation error (e.g. missing periods) |
| 401 | Invalid X-App-Password credentials |
| 500 | Unhandled external/internal exception |
