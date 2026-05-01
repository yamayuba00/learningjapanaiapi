# UID Migration Guide - Using UID for All Relations

## Overview

Sistem telah diupdate untuk menggunakan **UID (UUID) untuk semua relasi** instead of integer IDs. Ini meningkatkan security dan consistency across the entire API.

## What Changed

### 1. Database Schema

**user_credits table - Added column:**
```sql
user_uid UUID NOT NULL
FOREIGN KEY (user_uid) REFERENCES users(uid) ON DELETE CASCADE
INDEX (user_uid)
```

**Before:**
```sql
user_credits
├── id (PK)
├── uid (UUID)
├── user_id (FK -> users.id)  ❌ Integer ID
├── credits
└── ...
```

**After:**
```sql
user_credits
├── id (PK)
├── uid (UUID)
├── user_id (FK -> users.id)      # Kept for backward compatibility
├── user_uid (FK -> users.uid)    ✅ UUID relation
├── credits
└── ...
```

### 2. Model Relationships

**UserCredit Model:**
```php
// Before
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);  // Uses user_id
}

// After
public function user(): BelongsTo
{
    return $this->belongsTo(User::class, 'user_uid', 'uid');  // Uses user_uid
}
```

**User Model:**
```php
// Before
public function credit(): HasOne
{
    return $this->hasOne(UserCredit::class);  // Uses user_id
}

// After
public function credit(): HasOne
{
    return $this->hasOne(UserCredit::class, 'user_uid', 'uid');  // Uses user_uid
}
```

### 3. Repository Methods

**Changed parameter types from `int $userId` to `string $userUid`:**

```php
// Before
findByUserId(int $userId): ?UserCredit
addCredits(int $userId, int $amount): bool
deductCredits(int $userId, int $amount): bool
updateStreak(int $userId): bool
resetCycle(int $userId): bool

// After
findByUserUid(string $userUid): ?UserCredit
addCredits(string $userUid, int $amount): bool
deductCredits(string $userUid, int $amount): bool
updateStreak(string $userUid): bool
resetCycle(string $userUid): bool
```

### 4. Controller Methods

**Changed parameter types:**

```php
// Before
showByUserId(int $userId): JsonResponse
addCredits(Request $request, int $userId): JsonResponse
deductCredits(Request $request, int $userId): JsonResponse
updateStreak(int $userId): JsonResponse
resetCycle(int $userId): JsonResponse

// After
showByUserUid(string $userUid): JsonResponse
addCredits(Request $request, string $userUid): JsonResponse
deductCredits(Request $request, string $userUid): JsonResponse
updateStreak(string $userUid): JsonResponse
resetCycle(string $userUid): JsonResponse
```

### 5. API Routes

**Changed route parameters:**

```php
// Before
GET  /api/user-credits/user/{userId}
POST /api/user-credits/user/{userId}/add-credits
POST /api/user-credits/user/{userId}/deduct-credits
POST /api/user-credits/user/{userId}/update-streak
POST /api/user-credits/user/{userId}/reset-cycle

// After
GET  /api/user-credits/user/{userUid}
POST /api/user-credits/user/{userUid}/add-credits
POST /api/user-credits/user/{userUid}/deduct-credits
POST /api/user-credits/user/{userUid}/update-streak
POST /api/user-credits/user/{userUid}/reset-cycle
```

### 6. Service Layer

**AuthService - Updated UserCredit creation:**

```php
// Before
UserCredit::create([
    'user_id' => $user->id,
    'credits' => 0,
    ...
]);

// After
UserCredit::create([
    'user_id' => $user->id,
    'user_uid' => $user->uid,  // ✅ Added
    'credits' => 0,
    ...
]);
```

## Migration Steps

### 1. Run Migration

```bash
php artisan migrate
```

This will:
- Add `user_uid` column to `user_credits` table
- Populate `user_uid` from existing `user_id` relationships
- Add foreign key constraint
- Add index on `user_uid`

### 2. Update Existing Code

If you have existing code that uses the old API:

**Before:**
```javascript
// Using integer user ID
const userId = 1;
fetch(`/api/user-credits/user/${userId}/add-credits`, {
  method: 'POST',
  body: JSON.stringify({ amount: 100 })
});
```

**After:**
```javascript
// Using user UID
const userUid = 'ba198d82-43dd-11f1-94ea-4cedfb0ae7ce';
fetch(`/api/user-credits/user/${userUid}/add-credits`, {
  method: 'POST',
  body: JSON.stringify({ amount: 100 })
});
```

### 3. Test Data

Run seeder to create test data:

```bash
php artisan db:seed --class=UserCreditSeeder
```

**Output:**
```
User credits seeded successfully!
Test User 1 UID: ba198d82-43dd-11f1-94ea-4cedfb0ae7ce
Test User 2 UID: ba19a393-43dd-11f1-94ea-4cedfb0ae7ce
Test User 1 Credit UID: 1de98089-3228-4870-a650-ddf09bef75b1
Test User 2 Credit UID: 212405c5-6afe-4335-b847-d201a85cf5c4
```

## API Examples

### Get User Credit by User UID

**Before:**
```bash
GET /api/user-credits/user/1
```

**After:**
```bash
GET /api/user-credits/user/ba198d82-43dd-11f1-94ea-4cedfb0ae7ce
```

**Response:**
```json
{
  "success": true,
  "message": "User credit retrieved successfully",
  "data": {
    "id": 1,
    "uid": "1de98089-3228-4870-a650-ddf09bef75b1",
    "user_id": 1,
    "user_uid": "ba198d82-43dd-11f1-94ea-4cedfb0ae7ce",
    "credits": 100,
    "total_points": 500,
    ...
  }
}
```

### Add Credits by User UID

**Before:**
```bash
POST /api/user-credits/user/1/add-credits
Content-Type: application/json

{
  "amount": 100
}
```

**After:**
```bash
POST /api/user-credits/user/ba198d82-43dd-11f1-94ea-4cedfb0ae7ce/add-credits
Content-Type: application/json

{
  "amount": 100
}
```

### Update Streak by User UID

**Before:**
```bash
POST /api/user-credits/user/1/update-streak
```

**After:**
```bash
POST /api/user-credits/user/ba198d82-43dd-11f1-94ea-4cedfb0ae7ce/update-streak
```

## Benefits

### 1. Security
- ✅ No exposure of sequential integer IDs
- ✅ Harder to enumerate users
- ✅ Consistent UUID usage across all relations

### 2. Consistency
- ✅ All external APIs use UUIDs
- ✅ No mixing of IDs and UIDs
- ✅ Clearer API design

### 3. Scalability
- ✅ UUIDs are globally unique
- ✅ Better for distributed systems
- ✅ No ID collision issues

### 4. Developer Experience
- ✅ Consistent parameter naming (`userUid` everywhere)
- ✅ Clear distinction between internal ID and external UID
- ✅ Better type safety with string UIDs

## Backward Compatibility

**Note:** The `user_id` column is **kept** in the database for:
- Internal queries that need integer IDs
- Database joins and indexes
- Backward compatibility with existing data

However, **all API endpoints now use UIDs exclusively**.

## Testing

### Test User Credit Relationship

```php
// Get user with credit
$user = User::where('uid', 'ba198d82-43dd-11f1-94ea-4cedfb0ae7ce')->first();
$credit = $user->credit;  // Uses user_uid relation

echo $credit->user_uid;  // ba198d82-43dd-11f1-94ea-4cedfb0ae7ce
echo $credit->user->name;  // Test User 1
```

### Test Credit to User Relationship

```php
// Get credit with user
$credit = UserCredit::where('uid', '1de98089-3228-4870-a650-ddf09bef75b1')->first();
$user = $credit->user;  // Uses user_uid relation

echo $user->uid;  // ba198d82-43dd-11f1-94ea-4cedfb0ae7ce
echo $user->name;  // Test User 1
```

## Complete API Endpoints

### Using User UID

```
GET    /api/user-credits/user/{userUid}
POST   /api/user-credits/user/{userUid}/add-credits
POST   /api/user-credits/user/{userUid}/deduct-credits
POST   /api/user-credits/user/{userUid}/update-streak
POST   /api/user-credits/user/{userUid}/reset-cycle
```

### Using Credit UID

```
GET    /api/user-credits/uid/{uid}
PUT    /api/user-credits/uid/{uid}
DELETE /api/user-credits/uid/{uid}
POST   /api/user-credits/uid/{uid}/add-credits
POST   /api/user-credits/uid/{uid}/deduct-credits
POST   /api/user-credits/uid/{uid}/update-streak
POST   /api/user-credits/uid/{uid}/reset-cycle
```

### Using Internal ID (for admin/internal use)

```
GET    /api/user-credits/id/{id}
PUT    /api/user-credits/id/{id}
DELETE /api/user-credits/id/{id}
```

## Troubleshooting

### Issue: "User credit not found" when using user UID

**Solution:** Make sure you're using the user's UID, not the credit's UID.

```bash
# Wrong - using credit UID
GET /api/user-credits/user/1de98089-3228-4870-a650-ddf09bef75b1

# Correct - using user UID
GET /api/user-credits/user/ba198d82-43dd-11f1-94ea-4cedfb0ae7ce
```

### Issue: Relationship returns null

**Solution:** Ensure `user_uid` is populated in database.

```sql
-- Check if user_uid is populated
SELECT id, uid, user_id, user_uid FROM user_credits;

-- If null, run migration again
php artisan migrate:refresh
```

### Issue: Foreign key constraint error

**Solution:** Ensure all users have UIDs before running migration.

```sql
-- Check if all users have UIDs
SELECT id, uid FROM users WHERE uid IS NULL;

-- If any null, generate UIDs
UPDATE users SET uid = UUID() WHERE uid IS NULL;
```

## Summary

✅ **All relations now use UID instead of ID**
✅ **API endpoints updated to use `userUid` parameter**
✅ **Models updated with proper foreign key relationships**
✅ **Repository and Service layers updated**
✅ **Backward compatibility maintained with `user_id` column**
✅ **Test data and seeder updated**

---

**Version:** 2.0  
**Date:** April 29, 2026  
**Status:** ✅ Completed
