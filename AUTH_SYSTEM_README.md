# Authentication System Documentation

## Overview

Sistem Authentication lengkap menggunakan **Laravel Sanctum** dengan fitur:
- ✅ Register dengan email verification
- ✅ Login dengan token-based authentication
- ✅ Logout (single device & all devices)
- ✅ Refresh Token
- ✅ Email Verification
- ✅ Resend Verification Email
- ✅ User Profile Management
- ✅ Change Password
- ✅ Block/Unblock User Account
- ✅ UUID Support
- ✅ Auto-create User Credits on registration

## Database Schema

### Users Table (Updated)

| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Primary key |
| uid | UUID (UNIQUE) | Universal unique identifier |
| name | STRING | User's full name |
| email | STRING (UNIQUE) | User's email address |
| password | STRING | Hashed password |
| phone_number | STRING (NULLABLE) | Phone number |
| instagram | STRING (NULLABLE) | Instagram username |
| avatar_url | STRING (NULLABLE) | Profile picture URL |
| referal_code | STRING (UNIQUE) | User's referral code |
| referal_by_code | STRING (UNIQUE) | Referred by code |
| email_verified_at | TIMESTAMP (NULLABLE) | Email verification timestamp |
| email_verification_token | STRING (NULLABLE) | Email verification token |
| email_verification_sent_at | TIMESTAMP (NULLABLE) | When verification email was sent |
| last_login | TIMESTAMP (NULLABLE) | Last login timestamp |
| is_blocked | BOOLEAN | Account blocked status |
| blocked_at | TIMESTAMP (NULLABLE) | When account was blocked |
| blocked_reason | STRING (NULLABLE) | Reason for blocking |
| remember_token | STRING (NULLABLE) | Remember me token |
| created_at | TIMESTAMP | Account creation time |
| updated_at | TIMESTAMP | Last update time |

### Personal Access Tokens Table (Sanctum)

| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Primary key |
| tokenable_type | STRING | Model type (User) |
| tokenable_id | INT | User ID |
| name | STRING | Token name |
| token | STRING (UNIQUE) | Hashed token |
| abilities | TEXT | Token abilities/permissions |
| last_used_at | TIMESTAMP | Last token usage |
| expires_at | TIMESTAMP | Token expiration |
| created_at | TIMESTAMP | Token creation time |
| updated_at | TIMESTAMP | Last update time |

## Architecture

```
Controller → Service → Model
     ↓
ResponseHelper
```

### Files Structure

```
app/
├── Services/
│   └── AuthService.php                    # Authentication business logic
├── Http/Controllers/
│   └── AuthController.php                 # Authentication endpoints
├── Models/
│   └── User.php                          # User model (updated)
└── Helpers/
    └── ResponseHelper.php                # Response helper

config/
└── sanctum.php                           # Sanctum configuration

database/migrations/
├── 2026_04_29_150620_create_personal_access_tokens_table.php
└── 2026_04_29_150638_add_auth_fields_to_users_table.php
```

## API Endpoints

### Base URL: `/api/auth`

---

### 1. Register

**Endpoint:** `POST /api/auth/register`

**Description:** Register a new user account

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone_number": "081234567890",
  "instagram": "@johndoe",
  "avatar_url": "https://example.com/avatar.jpg",
  "referal_code": "JOHN123",
  "referal_by_code": "REF001"
}
```

**Required Fields:**
- `name` (string, max: 255)
- `email` (string, email, unique)
- `password` (string, min: 8, confirmed)

**Optional Fields:**
- `phone_number` (string, max: 20)
- `instagram` (string, max: 255)
- `avatar_url` (string, url)
- `referal_code` (string, unique) - Auto-generated if not provided
- `referal_by_code` (string, must exist in users table)

**Response (201):**
```json
{
  "success": true,
  "message": "Registration successful. Please check your email to verify your account.",
  "data": {
    "user": {
      "id": 1,
      "uid": "9d501d4e-442a-da54-82b6-0f5e237e0f36",
      "name": "John Doe",
      "email": "john@example.com",
      "phone_number": "081234567890",
      "instagram": "@johndoe",
      "avatar_url": "https://example.com/avatar.jpg",
      "referal_code": "JOHN123",
      "referal_by_code": "REF001",
      "email_verified_at": null,
      "last_login": null,
      "is_blocked": false,
      "blocked_at": null,
      "blocked_reason": null,
      "created_at": "2026-04-29T15:00:00.000000Z",
      "updated_at": "2026-04-29T15:00:00.000000Z"
    }
  }
}
```

**Notes:**
- User credit record is automatically created with 0 credits
- Email verification token is generated and sent
- Referral code is auto-generated if not provided (format: REF + 6 random chars)

---

### 2. Login

**Endpoint:** `POST /api/auth/login`

**Description:** Login to get access token

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "uid": "9d501d4e-442a-da54-82b6-0f5e237e0f36",
      "name": "John Doe",
      "email": "john@example.com",
      ...
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz1234567890",
    "token_type": "Bearer"
  }
}
```

**Error Responses:**

**Invalid Credentials (401):**
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

**Account Blocked (401):**
```json
{
  "success": false,
  "message": "Your account has been blocked. Reason: Violation of terms"
}
```

**Notes:**
- `last_login` timestamp is updated on successful login
- Token never expires by default (can be configured in sanctum.php)

---

### 3. Logout

**Endpoint:** `POST /api/auth/logout`

**Description:** Logout and revoke token(s)

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body (Optional):**
```json
{
  "all_devices": false
}
```

**Parameters:**
- `all_devices` (boolean, default: false) - If true, revokes all user tokens

**Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

**Or (if all_devices = true):**
```json
{
  "success": true,
  "message": "Logged out from all devices successfully"
}
```

---

### 4. Refresh Token

**Endpoint:** `POST /api/auth/refresh-token`

**Description:** Refresh access token (revokes current, issues new)

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Token refreshed successfully",
  "data": {
    "token": "2|newtoken1234567890abcdefghijklmnopqrstuvwxyz",
    "token_type": "Bearer"
  }
}
```

**Notes:**
- Current token is revoked
- New token is issued
- Use new token for subsequent requests

---

### 5. Verify Email

**Endpoint:** `GET /api/auth/verify-email/{token}`

**Description:** Verify user email address

**Parameters:**
- `token` (string, required) - Email verification token from email

**Response (200):**
```json
{
  "success": true,
  "message": "Email verified successfully"
}
```

**Error Responses:**

**Invalid Token (400):**
```json
{
  "success": false,
  "message": "Invalid verification token"
}
```

**Already Verified (400):**
```json
{
  "success": false,
  "message": "Email already verified"
}
```

**Token Expired (400):**
```json
{
  "success": false,
  "message": "Verification token has expired. Please request a new one."
}
```

**Notes:**
- Token expires after 24 hours
- Sets `email_verified_at` timestamp
- Clears `email_verification_token`

---

### 6. Resend Verification Email

**Endpoint:** `POST /api/auth/resend-verification`

**Description:** Resend email verification link

**Request Body:**
```json
{
  "email": "john@example.com"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Verification email sent successfully"
}
```

**Error Responses:**

**User Not Found (400):**
```json
{
  "success": false,
  "message": "User not found"
}
```

**Already Verified (400):**
```json
{
  "success": false,
  "message": "Email already verified"
}
```

---

### 7. Get Profile

**Endpoint:** `GET /api/auth/profile`

**Description:** Get authenticated user profile

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Profile retrieved successfully",
  "data": {
    "id": 1,
    "uid": "9d501d4e-442a-da54-82b6-0f5e237e0f36",
    "name": "John Doe",
    "email": "john@example.com",
    "phone_number": "081234567890",
    "instagram": "@johndoe",
    "avatar_url": "https://example.com/avatar.jpg",
    "referal_code": "JOHN123",
    "referal_by_code": "REF001",
    "email_verified_at": "2026-04-29T15:30:00.000000Z",
    "last_login": "2026-04-29T16:00:00.000000Z",
    "is_blocked": false,
    "blocked_at": null,
    "blocked_reason": null,
    "created_at": "2026-04-29T15:00:00.000000Z",
    "updated_at": "2026-04-29T16:00:00.000000Z",
    "credit": {
      "id": 1,
      "uid": "8c401c3d-331b-3760-9540-ccf08adf64a2",
      "user_id": 1,
      "credits": 100,
      "total_points": 500,
      "streak": 5,
      ...
    }
  }
}
```

**Notes:**
- Includes user credit information
- Use `$request->user()` to get authenticated user

---

### 8. Update Profile

**Endpoint:** `PUT /api/auth/profile`

**Description:** Update user profile information

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "John Updated",
  "phone_number": "081234567899",
  "instagram": "@johnupdated",
  "avatar_url": "https://example.com/new-avatar.jpg"
}
```

**All Fields Optional:**
- `name` (string, max: 255)
- `phone_number` (string, max: 20)
- `instagram` (string, max: 255)
- `avatar_url` (string, url)

**Response (200):**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "uid": "9d501d4e-442a-da54-82b6-0f5e237e0f36",
    "name": "John Updated",
    ...
  }
}
```

**Notes:**
- Email and password cannot be updated via this endpoint
- Use separate endpoints for those operations

---

### 9. Change Password

**Endpoint:** `POST /api/auth/change-password`

**Description:** Change user password

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "current_password": "oldpassword123",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```

**Required Fields:**
- `current_password` (string)
- `new_password` (string, min: 8, confirmed)

**Response (200):**
```json
{
  "success": true,
  "message": "Password changed successfully"
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Current password is incorrect"
}
```

**Notes:**
- All tokens except current are revoked
- User stays logged in with current token
- Must provide correct current password

---

### 10. Block User (Admin)

**Endpoint:** `POST /api/auth/block-user/{uid}`

**Description:** Block a user account

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Parameters:**
- `uid` (string, required) - User UID to block

**Request Body:**
```json
{
  "reason": "Violation of terms and conditions"
}
```

**Optional Fields:**
- `reason` (string, max: 500, default: "Violation of terms")

**Response (200):**
```json
{
  "success": true,
  "message": "User blocked successfully"
}
```

**Error Responses:**

**User Not Found (400):**
```json
{
  "success": false,
  "message": "User not found"
}
```

**Already Blocked (400):**
```json
{
  "success": false,
  "message": "User is already blocked"
}
```

**Notes:**
- All user tokens are revoked
- User cannot login until unblocked
- Sets `is_blocked`, `blocked_at`, and `blocked_reason`

---

### 11. Unblock User (Admin)

**Endpoint:** `POST /api/auth/unblock-user/{uid}`

**Description:** Unblock a user account

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Parameters:**
- `uid` (string, required) - User UID to unblock

**Response (200):**
```json
{
  "success": true,
  "message": "User unblocked successfully"
}
```

**Error Responses:**

**User Not Found (400):**
```json
{
  "success": false,
  "message": "User not found"
}
```

**Not Blocked (400):**
```json
{
  "success": false,
  "message": "User is not blocked"
}
```

**Notes:**
- Clears `is_blocked`, `blocked_at`, and `blocked_reason`
- User can login again
- Previous tokens remain revoked

---

## Authentication Flow

### Registration Flow

```
1. User submits registration form
2. System validates input
3. System generates UID and referral code (if not provided)
4. System creates user account
5. System creates user credit record (0 credits)
6. System generates email verification token
7. System sends verification email
8. User receives email with verification link
9. User clicks link → Email verified
```

### Login Flow

```
1. User submits email and password
2. System validates credentials
3. System checks if account is blocked
4. System updates last_login timestamp
5. System generates access token
6. User receives token
7. User includes token in subsequent requests
```

### Token Usage

```
Client Request:
GET /api/auth/profile
Headers:
  Authorization: Bearer {token}

Server Response:
{
  "success": true,
  "data": {...}
}
```

## Service Methods

### AuthService

```php
// Registration
register(array $data): array

// Authentication
login(array $credentials): array
logout(User $user, bool $allDevices = false): array
refreshToken(User $user): array

// Email Verification
verifyEmail(string $token): array
resendVerificationEmail(string $email): array

// Account Management
blockUser(string $uid, string $reason = 'Violation of terms'): array
unblockUser(string $uid): array

// Profile
getProfile(User $user): array
updateProfile(User $user, array $data): array
changePassword(User $user, string $currentPassword, string $newPassword): array

// Helpers
generateReferralCode(): string (private)
sendVerificationEmail(User $user, string $token): void (private)
```

## Configuration

### Sanctum Configuration

File: `config/sanctum.php`

```php
// Token expiration (null = never expires)
'expiration' => null,

// Token prefix
'prefix' => 'sanctum',

// Middleware
'middleware' => [
    'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
    'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
],
```

### Environment Variables

```env
# Application
APP_URL=http://localhost

# Mail Configuration (for email verification)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Security Features

### 1. Password Hashing
- Uses bcrypt hashing
- Automatic via Laravel's `hashed` cast
- Minimum 8 characters required

### 2. Token Security
- Tokens are hashed in database
- Plain text token only shown once (on creation)
- Tokens can be revoked individually or all at once

### 3. Email Verification
- Token expires after 24 hours
- Token is single-use
- Token is cleared after verification

### 4. Account Blocking
- Blocked users cannot login
- All tokens are revoked on block
- Reason is stored for reference

### 5. UUID Usage
- External APIs use UUID instead of ID
- Prevents enumeration attacks
- Better security for public endpoints

## Error Handling

### Common Error Responses

**Validation Error (422):**
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

**Unauthorized (401):**
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

**Not Found (404):**
```json
{
  "success": false,
  "message": "Resource not found"
}
```

**Server Error (500):**
```json
{
  "success": false,
  "message": "Internal server error"
}
```

## Testing

### Postman Collection

**Environment Variables:**
```
base_url: http://localhost:8000/api
token: (set after login)
```

**Test Sequence:**

1. **Register**
   ```
   POST {{base_url}}/auth/register
   ```

2. **Login**
   ```
   POST {{base_url}}/auth/login
   Save token to environment
   ```

3. **Get Profile**
   ```
   GET {{base_url}}/auth/profile
   Headers: Authorization: Bearer {{token}}
   ```

4. **Update Profile**
   ```
   PUT {{base_url}}/auth/profile
   Headers: Authorization: Bearer {{token}}
   ```

5. **Refresh Token**
   ```
   POST {{base_url}}/auth/refresh-token
   Headers: Authorization: Bearer {{token}}
   Update token in environment
   ```

6. **Logout**
   ```
   POST {{base_url}}/auth/logout
   Headers: Authorization: Bearer {{token}}
   ```

## Best Practices

### 1. Token Management
- Store tokens securely (not in localStorage for web)
- Include token in Authorization header
- Refresh tokens before expiration
- Revoke tokens on logout

### 2. Password Security
- Enforce minimum 8 characters
- Require password confirmation
- Hash passwords (automatic)
- Never log or display passwords

### 3. Email Verification
- Send verification email immediately
- Provide resend option
- Set reasonable expiration (24 hours)
- Clear token after verification

### 4. Error Messages
- Don't reveal if email exists (on login)
- Use generic messages for security
- Log detailed errors server-side
- Return user-friendly messages

### 5. Rate Limiting
- Implement rate limiting on login
- Limit verification email resends
- Protect against brute force attacks
- Use Laravel's built-in throttle middleware

## Future Enhancements

- [ ] Two-Factor Authentication (2FA)
- [ ] Social Login (Google, Facebook)
- [ ] Password Reset via Email
- [ ] Account Deletion
- [ ] Login History
- [ ] Device Management
- [ ] Role-Based Access Control (RBAC)
- [ ] API Rate Limiting
- [ ] Audit Logging
- [ ] Email Templates
- [ ] SMS Verification
- [ ] OAuth2 Support

---

**Version:** 1.0  
**Date:** April 29, 2026  
**Status:** ✅ Production Ready
