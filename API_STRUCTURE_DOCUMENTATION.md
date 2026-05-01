# API Structure Documentation

## 📋 Overview

API ini dipisahkan menjadi 2 bagian utama:
1. **Mobile App API** (`/api/mobile/*`) - Untuk pengguna mobile app
2. **CMS API** (`/api/cms/*`) - Untuk admin CMS

## 🎯 Base URLs

```
Mobile App: http://your-domain.com/api/mobile
CMS Admin:  http://your-domain.com/api/cms
```

---

## 📱 MOBILE APP API

### Authentication

#### Register
```http
POST /api/mobile/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone_number": "081234567890",
  "instagram": "@johndoe"
}
```

#### Login
```http
POST /api/mobile/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {...},
    "token": "1|xxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

#### Verify Email
```http
GET /api/mobile/auth/verify-email/{token}
```

#### Resend Verification
```http
POST /api/mobile/auth/resend-verification
Content-Type: application/json

{
  "email": "john@example.com"
}
```

#### Logout
```http
POST /api/mobile/auth/logout
Authorization: Bearer {token}
```

#### Refresh Token
```http
POST /api/mobile/auth/refresh-token
Authorization: Bearer {token}
```

#### Get Profile
```http
GET /api/mobile/auth/profile
Authorization: Bearer {token}
```

#### Update Profile
```http
PUT /api/mobile/auth/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Doe Updated",
  "phone_number": "081234567890",
  "instagram": "@johndoe",
  "avatar_url": "https://example.com/avatar.jpg"
}
```

#### Change Password
```http
POST /api/mobile/auth/change-password
Authorization: Bearer {token}
Content-Type: application/json

{
  "current_password": "oldpassword",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```

---

### User Credits

#### Get My Credits
```http
GET /api/mobile/credits
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Credit retrieved successfully",
  "data": {
    "uid": "xxx-xxx-xxx",
    "credits": 150,
    "total_points": 500,
    "streak": 5,
    "cycle_number": 2,
    "cycle_start_date": "2026-04-20",
    "last_claim_date": "2026-04-29"
  }
}
```

#### Get Balance Only
```http
GET /api/mobile/credits/balance
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "credits": 150,
    "total_points": 500
  }
}
```

#### Get Streak Info
```http
GET /api/mobile/credits/streak
Authorization: Bearer {token}
```

#### Get Cycle Info
```http
GET /api/mobile/credits/cycle
Authorization: Bearer {token}
```

---

### Daily Login

#### Get Daily Login Status
```http
GET /api/mobile/daily-login/status
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "can_claim_today": true,
    "current_cycle": 2,
    "current_day": 3,
    "cycle_progress": "3/7",
    "streak": 5,
    "last_claim_date": "2026-04-28",
    "next_reward": {
      "credits": 10,
      "points": 20
    },
    "cycle_claims": [...]
  }
}
```

#### Claim Daily Reward
```http
POST /api/mobile/daily-login/claim
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Day 4 reward claimed successfully!",
  "data": {
    "claim": {...},
    "reward": {
      "credits": 10,
      "points": 20
    },
    "cycle_completed": false
  }
}
```

#### Check Can Claim
```http
GET /api/mobile/daily-login/can-claim
Authorization: Bearer {token}
```

#### Get Claim History
```http
GET /api/mobile/daily-login/history?per_page=15
Authorization: Bearer {token}
```

---

## 🖥️ CMS (ADMIN) API

### Admin Authentication

#### Admin Login
```http
POST /api/cms/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "adminpassword"
}
```

#### Admin Logout
```http
POST /api/cms/auth/logout
Authorization: Bearer {token}
```

#### Get Admin Profile
```http
GET /api/cms/auth/profile
Authorization: Bearer {token}
```

---

### User Management

#### Get All Users
```http
GET /api/cms/users?per_page=15&search=john&status=active
Authorization: Bearer {token}
```

**Query Parameters:**
- `per_page` (optional): Items per page (default: 15)
- `search` (optional): Search by name or email
- `status` (optional): Filter by status (active, blocked, unverified)

#### Get User Details
```http
GET /api/cms/users/{userUid}
Authorization: Bearer {token}
```

#### Block User
```http
POST /api/cms/users/{userUid}/block
Authorization: Bearer {token}
Content-Type: application/json

{
  "reason": "Violation of terms of service"
}
```

#### Unblock User
```http
POST /api/cms/users/{userUid}/unblock
Authorization: Bearer {token}
```

---

### Credits Management

#### Get All Credits
```http
GET /api/cms/credits?per_page=15
Authorization: Bearer {token}
```

#### Get Top Users
```http
GET /api/cms/credits/top-users?limit=10
Authorization: Bearer {token}
```

#### Get Credit Statistics
```http
GET /api/cms/credits/statistics
Authorization: Bearer {token}
```

#### Get User Credit
```http
GET /api/cms/credits/user/{userUid}
Authorization: Bearer {token}
```

#### Add Credits to User
```http
POST /api/cms/credits/user/{userUid}/add
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 50,
  "reason": "Bonus reward"
}
```

#### Deduct Credits from User
```http
POST /api/cms/credits/user/{userUid}/deduct
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 20,
  "reason": "Certificate purchase"
}
```

#### Add Points to User
```http
POST /api/cms/credits/user/{userUid}/add-points
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 100,
  "reason": "Completed lesson"
}
```

#### Update User Streak
```http
POST /api/cms/credits/user/{userUid}/update-streak
Authorization: Bearer {token}
Content-Type: application/json

{
  "streak": 10
}
```

#### Reset User Cycle
```http
POST /api/cms/credits/user/{userUid}/reset-cycle
Authorization: Bearer {token}
```

---

### Daily Login Management

#### Get User Daily Login Status
```http
GET /api/cms/daily-login/user/{userUid}/status
Authorization: Bearer {token}
```

#### Get User Claim History
```http
GET /api/cms/daily-login/user/{userUid}/history?per_page=15
Authorization: Bearer {token}
```

#### Manual Claim for User
```http
POST /api/cms/daily-login/user/{userUid}/manual-claim
Authorization: Bearer {token}
```

---

## 📊 Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {...}
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {...}
}
```

### Validation Error Response
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

## 🔐 Authentication

Semua endpoint yang memerlukan autentikasi harus menyertakan token di header:

```http
Authorization: Bearer {your-token-here}
```

Token didapatkan dari response login:
```json
{
  "token": "1|xxxxxxxxxxxxx",
  "token_type": "Bearer"
}
```

---

## 📝 Daily Login Reward System

### 7-Day Cycle Rewards

| Day | Credits | Points |
|-----|---------|--------|
| 1   | 5       | 10     |
| 2   | 5       | 10     |
| 3   | 10      | 20     |
| 4   | 10      | 20     |
| 5   | 15      | 30     |
| 6   | 15      | 30     |
| 7   | 25      | 50     |

**Total per cycle**: 85 credits, 170 points

### Rules:
- User dapat claim 1x per hari
- Jika skip 1 hari, streak reset dan mulai dari Day 1
- Setelah Day 7, cycle baru dimulai
- Streak terus bertambah selama consecutive days

---

## 🚀 Testing

### Using cURL

**Login:**
```bash
curl -X POST http://localhost:8000/api/mobile/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'
```

**Get Profile:**
```bash
curl -X GET http://localhost:8000/api/mobile/auth/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Claim Daily Reward:**
```bash
curl -X POST http://localhost:8000/api/mobile/daily-login/claim \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Using Postman

1. Import collection dari file ini
2. Set environment variable `base_url` = `http://localhost:8000/api`
3. Set environment variable `token` setelah login
4. Test semua endpoints

---

## 📂 File Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── Mobile/
│       │   ├── AuthController.php
│       │   ├── UserCreditController.php
│       │   └── DailyLoginController.php
│       └── CMS/
│           ├── AuthController.php
│           ├── UserCreditController.php
│           └── DailyLoginController.php
├── Services/
│   ├── AuthService.php
│   └── DailyLoginService.php
├── Repositories/
│   ├── UserCreditRepository.php
│   ├── UserCreditRepositoryInterface.php
│   ├── DailyLoginClaimRepository.php
│   └── DailyLoginClaimRepositoryInterface.php
└── Models/
    ├── User.php
    ├── UserCredit.php
    └── DailyLoginClaim.php
```

---

## ⚠️ Notes

1. **Legacy Routes**: Routes lama (`/api/auth/*`, `/api/my-credits/*`) masih tersedia untuk backward compatibility
2. **Admin Middleware**: Perlu ditambahkan middleware untuk memverifikasi role admin
3. **Rate Limiting**: Pertimbangkan untuk menambahkan rate limiting pada endpoints tertentu
4. **Pagination**: Semua list endpoints support pagination dengan parameter `per_page`

---

## 🔄 Next Features to Implement

- [ ] User Progress API
- [ ] JLPT Lessons & Tests API
- [ ] User Notes API
- [ ] Kanji API
- [ ] Vocabulary API
- [ ] Certificate API
- [ ] Ad Watch API
- [ ] Leaderboard API
