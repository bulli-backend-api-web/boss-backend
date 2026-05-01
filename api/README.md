# API Documentation (CodeIgniter 3)

Base URL: `http://localhost/project/api/v1`

---

## Authentication

All endpoints (except login) require an `Authorization` header:

```
Authorization: Bearer YOUR_TOKEN_HERE
```

You can also use the `X-API-Token: YOUR_TOKEN_HERE` header.

---

## Auth Endpoints

### Login
`POST /v1/auth/login`

**Body (JSON):**
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Response:**
```json
{
  "status": true,
  "code": 200,
  "message": "Login successful.",
  "data": {
    "user": { "id": 1, "username": "admin", "email": "admin@example.com", "role": "superadmin" },
    "token": "abc123..."
  }
}
```

---

## Products Endpoints

### List Products
`GET /v1/products`

**Query Params:** `page`, `per_page`, `search`

### Get Single Product
`GET /v1/products/{id}`

### Create Product
`POST /v1/products/store`

**Body (JSON):**
```json
{
  "name": "Product Name",
  "description": "Optional description",
  "price": 99.99,
  "status": 1
}
```

### Update Product
`POST /v1/products/update/{id}`

**Body (JSON) — send only fields to update:**
```json
{
  "name": "Updated Name",
  "price": 149.99
}
```

### Delete Product
`POST /v1/products/delete/{id}`

---

## Response Format

All endpoints return:
```json
{
  "status": true | false,
  "code": 200,
  "message": "...",
  "data": { ... } | null
}
```

## HTTP Status Codes
| Code | Meaning |
|------|---------|
| 200  | OK |
| 201  | Created |
| 400  | Bad Request |
| 401  | Unauthorized |
| 403  | Forbidden |
| 404  | Not Found |
| 405  | Method Not Allowed |
| 422  | Validation Error |
| 500  | Server Error |
