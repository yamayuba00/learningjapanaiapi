# Simplified API Guide - User Credits

## 🎯 Philosophy

**Credits are tied to users** - tidak perlu CRUD manual karena:
1. ✅ Credit **auto-created** saat user register
2. ✅ Credit **diakses via user profile** (`/api/auth/profile` sudah include credit)
3. ✅ Credit **lifecycle tied to user** (deleted when user deleted via CASCADE)

## 📊 API Structure

### Total: 21 Endpoints (down from 28)

**Authentication: 11 endpoints**
- Public: Register, Login, Verify Email, Resend Verification
- Protected: Logout, Refresh Token, Profile, Change Password, Block/Unblock User

**My Credits: 5 endpoints** (for authenticated user)
- Get my credit, Add, Deduct, Update Streak, Reset Cycle

**Admin Credits: 5 endpoints** (for managing other users)
- Get user credit, Add, Deduct, Update Streak, Reset Cycle

---

## 🔐 Authentication Endpoints (11)

### Public Routes

#### 1. Register
```http
POST /api/auth/register
```

#### 2. Login
```http
POST /api/auth/login
```

#### 3. Verify Email
```http
GET /api/auth/verify-email/{token}
```

#### 4. Resend Verification
```http
POST /api/auth/resend-verification
```

### Protected Routes (require Bearer token)

#### 5. Logout
```http
POST /api/auth/logout
```

#### 6. Refresh Token
```http
POST /api/auth/refresh-token
```

#### 7. Get Profile (includes credit!)
```http
GET /api/auth/profile
```

**Response:**
```json
{
  "success": true,
  "message": "Profile retrieved successfully",
  "data": {
    "id": 1,
    "uid": "ba198d82-43dd-11f1-94ea-4cedfb0ae7ce",
    "name": "John Doe",
    "email": "john@example.com",
    ...
    "credit": {
      "id": 1,
      "uid": "1de98089-3228-4870-a650-ddf09bef75b1",
      "user_uid": "ba198d82-43dd-11f1-94ea-4cedfb0ae7ce",
      "credits": 100,
      "total_points": 500,
      "streak": 5,
      ...
    }
  }
}
```

#### 8. Update Profile
```http
PUT /api/auth/profile
```

#### 9. Change Password
```http
POST /api/auth/change-password
```

#### 10. Block User (Admin)
```http
POST /api/auth/block-user/{uid}
```

#### 11. Unblock User (Admin)
```http
POST /api/auth/unblock-user/{uid}
```

---

## 💰 My Credits Endpoints (5)

**Base URL:** `/api/my-credits`

**Note:** Semua endpoint ini untuk **authenticated user** (menggunakan token dari login)

### 1. Get My Credit

```http
GET /api/my-credits
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "User credit retrieved successfully",
  "data": {
    "id": 1,
    "uid": "1de98089-3228-4870-a650-ddf09bef75b1",
    "user_uid": "ba198d82-43dd-11f1-94ea-4cedfb0ae7ce",
    "credits": 100,
    "total_points": 500,
    "streak": 5,
    "cycle_number": 1,
    "cycle_start_date": "2026-03-30",
    "last_claim_date": "2026-04-28",
    "created_at": "2026-04-29T15:00:00.000000Z",
    "updated_at": "2026-04-29T16:00:00.000000Z"
  }
}
```

### 2. Add Credits to My Account

```http
POST /api/my-credits/add
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 100
}
```

**Response:**
```json
{
  "success": true,
  "message": "Credits added successfully"
}
```

**Effect:**
- `credits` += 100
- `total_points` += 100

### 3. Deduct Credits from My Account

```http
POST /api/my-credits/deduct
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 50
}
```

**Response:**
```json
{
  "success": true,
  "message": "Credits deducted successfully"
}
```

**Effect:**
- `credits` -= 50
- `total_points` unchanged

**Error if insufficient balance:**
```json
{
  "success": false,
  "message": "Failed to deduct credits or insufficient balance"
}
```

### 4. Update My Streak

```http
POST /api/my-credits/update-streak
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Streak updated successfully"
}
```

**Logic:**
- If first claim: `streak = 1`
- If claimed yesterday: `streak += 1`
- If already claimed today: no change
- If missed days: `streak = 1` (reset)

### 5. Reset My Cycle

```http
POST /api/my-credits/reset-cycle
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Cycle reset successfully"
}
```

**Effect:**
- `cycle_number` += 1
- `cycle_start_date` = today
- `credits` = 0
- `streak` = 0

---

## 👨‍💼 Admin Credits Endpoints (5)

**Base URL:** `/api/admin/credits`

**Note:** Endpoint ini untuk **admin** mengelola credit user lain

### 1. Get User Credit by User UID

```http
GET /api/admin/credits/user/{userUid}
Authorization: Bearer {admin_token}
```

**Example:**
```http
GET /api/admin/credits/user/ba198d82-43dd-11f1-94ea-4cedfb0ae7ce
```

**Response:**
```json
{
  "success": true,
  "message": "User credit retrieved successfully",
  "data": {
    "id": 1,
    "uid": "1de98089-3228-4870-a650-ddf09bef75b1",
    "user_uid": "ba198d82-43dd-11f1-94ea-4cedfb0ae7ce",
    "credits": 100,
    ...
  }
}
```

### 2. Add Credits to User

```http
POST /api/admin/credits/user/{userUid}/add
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "amount": 100
}
```

**Example:**
```http
POST /api/admin/credits/user/ba198d82-43dd-11f1-94ea-4cedfb0ae7ce/add
```

### 3. Deduct Credits from User

```http
POST /api/admin/credits/user/{userUid}/deduct
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "amount": 50
}
```

### 4. Update User Streak

```http
POST /api/admin/credits/user/{userUid}/update-streak
Authorization: Bearer {admin_token}
```

### 5. Reset User Cycle

```http
POST /api/admin/credits/user/{userUid}/reset-cycle
Authorization: Bearer {admin_token}
```

---

## 🔄 Complete User Flow

### 1. Registration & Auto Credit Creation

```bash
# Step 1: Register
POST /api/auth/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

# Response includes user with UID
# Credit is AUTO-CREATED with 0 credits

# Step 2: Verify Email
GET /api/auth/verify-email/{token}

# Step 3: Login
POST /api/auth/login
{
  "email": "john@example.com",
  "password": "password123"
}

# Response includes token
{
  "token": "1|abcdefghijklmnopqrstuvwxyz",
  ...
}
```

### 2. Using Credits

```bash
# Get profile (includes credit)
GET /api/auth/profile
Authorization: Bearer {token}

# Or get credit directly
GET /api/my-credits
Authorization: Bearer {token}

# Add credits (e.g., after completing a task)
POST /api/my-credits/add
Authorization: Bearer {token}
{
  "amount": 50
}

# Update streak (daily check-in)
POST /api/my-credits/update-streak
Authorization: Bearer {token}

# Use credits (e.g., purchase something)
POST /api/my-credits/deduct
Authorization: Bearer {token}
{
  "amount": 30
}
```

### 3. Admin Managing User Credits

```bash
# Get user's credit
GET /api/admin/credits/user/ba198d82-43dd-11f1-94ea-4cedfb0ae7ce
Authorization: Bearer {admin_token}

# Give bonus credits
POST /api/admin/credits/user/ba198d82-43dd-11f1-94ea-4cedfb0ae7ce/add
Authorization: Bearer {admin_token}
{
  "amount": 100
}

# Reset user's cycle (e.g., monthly reset)
POST /api/admin/credits/user/ba198d82-43dd-11f1-94ea-4cedfb0ae7ce/reset-cycle
Authorization: Bearer {admin_token}
```

---

## 📝 Key Differences from Previous Version

### ❌ Removed Endpoints (7 endpoints)

1. ~~`GET /api/user-credits`~~ - No need to list all credits
2. ~~`POST /api/user-credits`~~ - Credit auto-created on register
3. ~~`GET /api/user-credits/id/{id}`~~ - Use profile or my-credits
4. ~~`PUT /api/user-credits/id/{id}`~~ - No manual update needed
5. ~~`DELETE /api/user-credits/id/{id}`~~ - Deleted with user (CASCADE)
6. ~~`GET /api/user-credits/uid/{uid}`~~ - Use my-credits or admin endpoint
7. ~~`PUT /api/user-credits/uid/{uid}`~~ - No manual update needed

### ✅ New Simplified Endpoints

**For Users:**
- `GET /api/my-credits` - Get my credit
- `POST /api/my-credits/add` - Add to my credit
- `POST /api/my-credits/deduct` - Deduct from my credit
- `POST /api/my-credits/update-streak` - Update my streak
- `POST /api/my-credits/reset-cycle` - Reset my cycle

**For Admins:**
- `GET /api/admin/credits/user/{userUid}` - Get user's credit
- `POST /api/admin/credits/user/{userUid}/add` - Add to user's credit
- `POST /api/admin/credits/user/{userUid}/deduct` - Deduct from user's credit
- `POST /api/admin/credits/user/{userUid}/update-streak` - Update user's streak
- `POST /api/admin/credits/user/{userUid}/reset-cycle` - Reset user's cycle

---

## 🎯 Benefits

### 1. Simpler API
- ✅ 21 endpoints instead of 28
- ✅ Clear separation: `/my-credits` vs `/admin/credits`
- ✅ No confusion about which endpoint to use

### 2. Better UX
- ✅ User doesn't need to know their credit UID
- ✅ Just use `/my-credits` for own operations
- ✅ Profile already includes credit info

### 3. Logical Structure
- ✅ Credit lifecycle tied to user
- ✅ No orphaned credits
- ✅ No manual CRUD needed

### 4. Security
- ✅ Users can only access their own credits
- ✅ Admin endpoints clearly separated
- ✅ Easy to add admin middleware later

---

## 🧪 Testing Examples

### Test as Regular User

```bash
# 1. Login
TOKEN=$(curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test1@example.com","password":"password"}' \
  | jq -r '.data.token')

# 2. Get my credit
curl -X GET http://localhost:8000/api/my-credits \
  -H "Authorization: Bearer $TOKEN"

# 3. Add credits
curl -X POST http://localhost:8000/api/my-credits/add \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"amount": 100}'

# 4. Update streak
curl -X POST http://localhost:8000/api/my-credits/update-streak \
  -H "Authorization: Bearer $TOKEN"

# 5. Deduct credits
curl -X POST http://localhost:8000/api/my-credits/deduct \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"amount": 50}'
```

### Test as Admin

```bash
# Get user's credit
curl -X GET http://localhost:8000/api/admin/credits/user/ba198d82-43dd-11f1-94ea-4cedfb0ae7ce \
  -H "Authorization: Bearer $ADMIN_TOKEN"

# Give bonus to user
curl -X POST http://localhost:8000/api/admin/credits/user/ba198d82-43dd-11f1-94ea-4cedfb0ae7ce/add \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"amount": 500}'
```

---

## 📚 Quick Reference

### User Endpoints
```
GET    /api/my-credits              # Get my credit
POST   /api/my-credits/add          # Add credits
POST   /api/my-credits/deduct       # Deduct credits
POST   /api/my-credits/update-streak # Update streak
POST   /api/my-credits/reset-cycle  # Reset cycle
```

### Admin Endpoints
```
GET    /api/admin/credits/user/{userUid}                # Get user credit
POST   /api/admin/credits/user/{userUid}/add           # Add credits
POST   /api/admin/credits/user/{userUid}/deduct        # Deduct credits
POST   /api/admin/credits/user/{userUid}/update-streak # Update streak
POST   /api/admin/credits/user/{userUid}/reset-cycle   # Reset cycle
```

### Auth Endpoints
```
POST   /api/auth/register           # Register
POST   /api/auth/login              # Login
GET    /api/auth/profile            # Get profile (includes credit!)
POST   /api/auth/logout             # Logout
```

---

## ✅ Summary

**Before:** 28 endpoints with confusing CRUD operations  
**After:** 21 endpoints with clear user/admin separation

**Philosophy:**
- Credit is **part of user**, not separate entity
- Users manage **their own** credits via `/my-credits`
- Admins manage **other users'** credits via `/admin/credits`
- No manual CRUD - credit lifecycle tied to user

**Result:** Simpler, clearer, more logical API! 🎉

---

**Version:** 3.0  
**Date:** April 29, 2026  
**Status:** ✅ Simplified & Production Ready
