# Referral Registration System API Documentation

Sistem registrasi dengan kode referral dan reward otomatis setelah verifikasi email.

## Overview

Flow registrasi dengan referral:
1. **Register** - User input data + optional referral code
2. **Email Verification** - User verifikasi email dengan OTP
3. **Reward Processing** - Sistem otomatis memberikan reward referral
4. **Login** - User bisa login setelah email terverifikasi

---

## Registration with Referral Code

### Endpoint
**POST** `/api/mobile/auth/register`

### Request Body
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone_number": "+6281234567890",
  "instagram": "@johndoe",
  "referral_code": "REF123456"
}
```

### Validation Rules
- `name`: required, string, max:255
- `email`: required, email, max:255, unique:users
- `password`: required, string, min:8, confirmed
- `phone_number`: nullable, string, max:20
- `instagram`: nullable, string, max:100
- `referral_code`: nullable, string, max:20, exists:users,referal_code

### Response Success
```json
{
  "success": true,
  "message": "Registration successful. Please check your email to verify your account.",
  "data": {
    "success": true,
    "user": {
      "uid": "uuid",
      "name": "John Doe",
      "email": "john@example.com",
      "referal_code": "REFABC123",
      "referal_by_code": "REF123456",
      "email_verified_at": null,
      "created_at": "2026-05-01T16:00:00.000000Z"
    },
    "message": "Registration successful. Please check your email for the verification code."
  }
}
```

### Response Error - Invalid Referral Code
```json
{
  "success": false,
  "message": "Registration failed: Invalid referral code"
}
```

---

## Email Verification with Reward Processing

### Endpoint
**POST** `/api/mobile/auth/verify-email-otp`

### Request Body
```json
{
  "email": "john@example.com",
  "otp": "123456"
}
```

### Response Success (With Referral Rewards)
```json
{
  "success": true,
  "message": "Email verified successfully",
  "data": null
}
```

### What Happens Behind the Scenes:
1. ✅ Email marked as verified
2. ✅ Referrer gets +100 credits
3. ✅ New user gets +40 credits
4. ✅ ReferralReward record created
5. ✅ User can now login

---

## Reward System

### Credit Distribution
| User Type | Credits Received | Points Received |
|-----------|------------------|-----------------|
| **Referrer** | +100 | +100 |
| **New User** | +40 | +40 |

### Database Updates

#### 1. UserCredit Table
```sql
-- Referrer credit update
UPDATE user_credits 
SET credits = credits + 100, total_points = total_points + 100 
WHERE user_uid = 'referrer_uid';

-- New user credit update  
UPDATE user_credits 
SET credits = credits + 40, total_points = total_points + 40 
WHERE user_uid = 'new_user_uid';
```

#### 2. ReferralReward Table
```sql
INSERT INTO referral_rewards (
  referrer_user_uid,
  referred_user_uid, 
  referrer_reward,
  referred_reward,
  status,
  processed_at
) VALUES (
  'referrer_uid',
  'new_user_uid',
  100,
  40,
  'completed',
  NOW()
);
```

---

## Login Requirement

### Email Verification Required
User **HARUS** verifikasi email sebelum bisa login.

### Login Response - Email Not Verified
```json
{
  "success": false,
  "message": "Please verify your email before logging in"
}
```

### Login Response - Success (After Verification)
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "success": true,
    "user": {
      "uid": "uuid",
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": "2026-05-01T16:30:00.000000Z",
      "referal_code": "REFABC123",
      "referal_by_code": "REF123456"
    },
    "token": "token_string",
    "token_type": "Bearer"
  }
}
```

---

## Complete Registration Flow

### 1. Registration
```javascript
const registerWithReferral = async (userData) => {
  const response = await api.post('/auth/register', {
    name: userData.name,
    email: userData.email,
    password: userData.password,
    password_confirmation: userData.password,
    phone_number: userData.phone,
    instagram: userData.instagram,
    referral_code: userData.referralCode // Optional
  });
  
  if (response.success) {
    // Navigate to OTP verification
    navigation.navigate('VerifyEmail', { email: userData.email });
  }
};
```

### 2. Email Verification
```javascript
const verifyEmail = async (email, otp) => {
  const response = await api.post('/auth/verify-email-otp', {
    email,
    otp
  });
  
  if (response.success) {
    // Email verified, rewards processed automatically
    showSuccess('Email verified! Welcome bonus added to your account.');
    navigation.navigate('Login');
  }
};
```

### 3. Login
```javascript
const login = async (email, password) => {
  const response = await api.post('/auth/login', {
    email,
    password
  });
  
  if (response.success) {
    // Login successful, user has verified email
    saveToken(response.data.token);
    navigation.navigate('Dashboard');
  } else if (response.message.includes('verify your email')) {
    // Redirect to email verification
    navigation.navigate('VerifyEmail', { email });
  }
};
```

---

## Error Handling

### Registration Errors
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "referral_code": ["The selected referral code is invalid."],
    "email": ["The email has already been taken."]
  }
}
```

### Verification Errors
```json
{
  "success": false,
  "message": "Invalid verification code"
}
```

### Login Errors
```json
{
  "success": false,
  "message": "Please verify your email before logging in"
}
```

---

## Security Features

### Referral Code Validation
- Referral code must exist in database
- Cannot use own referral code
- Rewards only processed once per user

### Email Verification Requirement
- Mandatory email verification before login
- OTP expires in 10 minutes
- Rewards only given after successful verification

### Transaction Safety
- All operations wrapped in database transactions
- Rollback on any failure
- Comprehensive error logging

---

## Database Schema

### Users Table Fields
```sql
referal_code VARCHAR(20) -- User's own referral code
referal_by_code VARCHAR(20) -- Code used to register (referrer's code)
email_verified_at TIMESTAMP -- Email verification status
```

### UserCredit Table Fields
```sql
user_uid VARCHAR(36) -- Reference to user
credits INT -- Current credits
total_points INT -- Lifetime points
```

### ReferralReward Table Fields
```sql
referrer_user_uid VARCHAR(36) -- Who referred
referred_user_uid VARCHAR(36) -- Who was referred  
referrer_reward INT -- Credits given to referrer
referred_reward INT -- Credits given to new user
status ENUM('pending', 'completed', 'failed')
processed_at TIMESTAMP -- When rewards were given
```

---

## Monitoring & Analytics

### Key Metrics
- Registration conversion rate with/without referral
- Email verification completion rate
- Referral code usage statistics
- Credit distribution tracking

### Important Logs
```
[INFO] User registered with referral: new_user_uid, referrer: referrer_uid
[INFO] Email verified and rewards processed: new_user_uid (+40), referrer_uid (+100)
[WARNING] Invalid referral code used: INVALID123
[ERROR] Referral reward processing failed: Database error
```

---

## Testing Scenarios

### 1. Registration without Referral
```bash
curl -X POST /api/mobile/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com", 
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### 2. Registration with Valid Referral
```bash
curl -X POST /api/mobile/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123", 
    "password_confirmation": "password123",
    "referral_code": "REF123456"
  }'
```

### 3. Email Verification
```bash
curl -X POST /api/mobile/auth/verify-email-otp \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "otp": "123456"
  }'
```

### 4. Login After Verification
```bash
curl -X POST /api/mobile/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

---

## Status Codes

- `200 OK`: Request successful
- `201 Created`: Registration successful  
- `400 Bad Request`: Validation error or invalid referral
- `401 Unauthorized`: Invalid credentials or email not verified
- `500 Internal Server Error`: Server error

---

## Production Considerations

### Rate Limiting
```php
// Limit registration attempts
Route::middleware('throttle:5,60')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
});
```

### Fraud Prevention
- Monitor suspicious referral patterns
- Limit rewards per IP/device
- Validate email domains
- Track referral chain depth

### Performance Optimization
- Index referral code columns
- Cache frequently used referral codes
- Batch process rewards if needed
- Monitor database transaction performance