# API Testing Guide - User Credits System

## Test Data

Setelah menjalankan seeder, Anda memiliki 2 test users dengan UIDs:

**Test User 1:**
- Email: test1@example.com
- UID: `1de98089-3228-4870-a650-ddf09bef75b1`
- Credits: 100
- Total Points: 500
- Streak: 5

**Test User 2:**
- Email: test2@example.com
- UID: `212405c5-6afe-4335-b847-d201a85cf5c4`
- Credits: 250
- Total Points: 1000
- Streak: 10

## Base URL

```
http://localhost/api/user-credits
```

atau jika menggunakan Laragon:

```
http://learningjapancms.test/api/user-credits
```

## Test Scenarios

### 1. Get All User Credits

```bash
GET /api/user-credits
```

**Expected Response:**
```json
{
  "success": true,
  "message": "User credits retrieved successfully",
  "data": [
    {
      "id": 1,
      "uid": "1de98089-3228-4870-a650-ddf09bef75b1",
      "user_id": 1,
      "credits": 100,
      "total_points": 500,
      "streak": 5,
      ...
    },
    ...
  ]
}
```

### 2. Get User Credit by UID

```bash
GET /api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1
```

**Expected Response:**
```json
{
  "success": true,
  "message": "User credit retrieved successfully",
  "data": {
    "id": 1,
    "uid": "1de98089-3228-4870-a650-ddf09bef75b1",
    "user_id": 1,
    "credits": 100,
    ...
  }
}
```

### 3. Get User Credit by ID

```bash
GET /api/user-credits/id/1
```

### 4. Get User Credit by User ID

```bash
GET /api/user-credits/user/1
```

### 5. Create New User Credit

**Prerequisites:** Create a new user first or use existing user without credits

```bash
POST /api/user-credits
Content-Type: application/json

{
  "user_id": 3
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "User credit created successfully",
  "data": {
    "id": 3,
    "uid": "auto-generated-uuid",
    "user_id": 3,
    "credits": 0,
    "total_points": 0,
    "streak": 0,
    "cycle_number": 1,
    ...
  }
}
```

### 6. Update User Credit by UID

```bash
PUT /api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1
Content-Type: application/json

{
  "credits": 150,
  "streak": 6
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "User credit updated successfully"
}
```

### 7. Add Credits by UID

```bash
POST /api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1/add-credits
Content-Type: application/json

{
  "amount": 50
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Credits added successfully"
}
```

**Result:** Credits: 100 + 50 = 150, Total Points: 500 + 50 = 550

### 8. Deduct Credits by UID

```bash
POST /api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1/deduct-credits
Content-Type: application/json

{
  "amount": 30
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Credits deducted successfully"
}
```

**Result:** Credits: 150 - 30 = 120

### 9. Update Streak by UID

```bash
POST /api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1/update-streak
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Streak updated successfully"
}
```

**Logic:**
- If last claim was yesterday: streak + 1
- If last claim was today: no change
- If last claim was before yesterday: streak = 1

### 10. Reset Cycle by UID

```bash
POST /api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1/reset-cycle
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Cycle reset successfully"
}
```

**Result:**
- cycle_number: incremented
- cycle_start_date: today
- credits: 0
- streak: 0

### 11. Delete User Credit by UID

```bash
DELETE /api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1
```

**Expected Response:**
```json
{
  "success": true,
  "message": "User credit deleted successfully"
}
```

## Error Scenarios

### 1. Not Found (404)

```bash
GET /api/user-credits/uid/invalid-uuid
```

**Response:**
```json
{
  "success": false,
  "message": "User credit not found"
}
```

### 2. Validation Error (422)

```bash
POST /api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1/add-credits
Content-Type: application/json

{
  "amount": -10
}
```

**Response:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "amount": ["The amount field must be at least 1."]
  }
}
```

### 3. Insufficient Balance (400)

```bash
POST /api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1/deduct-credits
Content-Type: application/json

{
  "amount": 999999
}
```

**Response:**
```json
{
  "success": false,
  "message": "Failed to deduct credits or insufficient balance"
}
```

### 4. Duplicate User Credit (422)

```bash
POST /api/user-credits
Content-Type: application/json

{
  "user_id": 1
}
```

**Response:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "user_id": ["The user id has already been taken."]
  }
}
```

## Postman Collection

### Environment Variables

```
base_url: http://localhost/api
test_uid_1: 1de98089-3228-4870-a650-ddf09bef75b1
test_uid_2: 212405c5-6afe-4335-b847-d201a85cf5c4
```

### Collection Structure

```
User Credits API
├── Get All Credits
├── Get by UID
├── Get by ID
├── Get by User ID
├── Create Credit
├── Update by UID
├── Delete by UID
├── Add Credits
├── Deduct Credits
├── Update Streak
└── Reset Cycle
```

## cURL Examples

### Get All
```bash
curl -X GET http://localhost/api/user-credits
```

### Get by UID
```bash
curl -X GET http://localhost/api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1
```

### Add Credits
```bash
curl -X POST http://localhost/api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1/add-credits \
  -H "Content-Type: application/json" \
  -d '{"amount": 50}'
```

### Update
```bash
curl -X PUT http://localhost/api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1 \
  -H "Content-Type: application/json" \
  -d '{"credits": 200, "streak": 10}'
```

## Testing Workflow

### Complete Test Flow

1. **Get initial state**
   ```bash
   GET /api/user-credits/uid/{uid}
   ```

2. **Add credits**
   ```bash
   POST /api/user-credits/uid/{uid}/add-credits
   Body: {"amount": 100}
   ```

3. **Verify credits added**
   ```bash
   GET /api/user-credits/uid/{uid}
   # Check: credits increased, total_points increased
   ```

4. **Update streak**
   ```bash
   POST /api/user-credits/uid/{uid}/update-streak
   ```

5. **Verify streak updated**
   ```bash
   GET /api/user-credits/uid/{uid}
   # Check: streak changed, last_claim_date updated
   ```

6. **Deduct credits**
   ```bash
   POST /api/user-credits/uid/{uid}/deduct-credits
   Body: {"amount": 50}
   ```

7. **Verify credits deducted**
   ```bash
   GET /api/user-credits/uid/{uid}
   # Check: credits decreased, total_points unchanged
   ```

8. **Reset cycle**
   ```bash
   POST /api/user-credits/uid/{uid}/reset-cycle
   ```

9. **Verify cycle reset**
   ```bash
   GET /api/user-credits/uid/{uid}
   # Check: cycle_number increased, credits = 0, streak = 0
   ```

## Quick Setup

1. **Run migrations:**
   ```bash
   php artisan migrate
   ```

2. **Run seeder:**
   ```bash
   php artisan db:seed --class=UserCreditSeeder
   ```

3. **Start server:**
   ```bash
   php artisan serve
   ```

4. **Test endpoint:**
   ```bash
   curl http://localhost:8000/api/user-credits
   ```

## Notes

- All responses follow consistent format with `success`, `message`, and optional `data`
- UID is auto-generated on creation
- Use UID for external/public APIs
- Use ID for internal operations
- All dates are in ISO 8601 format
- Timestamps are automatically managed by Laravel

## Troubleshooting

### Issue: 404 Not Found on all routes
**Solution:** Check if API routes are registered in `bootstrap/app.php`

### Issue: UID is null
**Solution:** Run `composer dump-autoload` and check model boot method

### Issue: Validation errors
**Solution:** Check request body format and required fields

### Issue: Database connection error
**Solution:** Check `.env` file database configuration

---

**Happy Testing! 🚀**
