# Referral System API Documentation

## Overview
The referral system allows users to invite others and earn rewards. When a new user signs up using a referral code, both the referrer and the referred user receive credits.

## Rewards Configuration
- **Referrer Reward**: 100 credits
- **Referred User Reward**: 40 credits

## Mobile API Endpoints

### 1. Validate Referral Code
Check if a referral code is valid before applying it.

**Endpoint**: `POST /api/mobile/referral/validate`

**Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body**:
```json
{
  "referral_code": "ABC12345"
}
```

**Success Response** (200):
```json
{
  "success": true,
  "message": "Referral code is valid",
  "data": {
    "valid": true,
    "referrer_email": "referrer@example.com",
    "referrer_name": "John Doe",
    "referrer_reward": 100,
    "referred_reward": 40
  }
}
```

**Invalid Code Response** (200):
```json
{
  "success": true,
  "message": "Referral code not found",
  "data": {
    "valid": false,
    "message": "Invalid referral code"
  }
}
```

**Own Code Response** (200):
```json
{
  "success": true,
  "message": "Invalid referral code",
  "data": {
    "valid": false,
    "message": "You cannot use your own referral code"
  }
}
```

---

### 2. Get My Referral Code
Get the current user's referral code to share with others.

**Endpoint**: `GET /api/mobile/referral/my-code`

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response** (200):
```json
{
  "success": true,
  "message": "Referral code retrieved successfully",
  "data": {
    "referral_code": "REF123ABC",
    "referrer_reward": 100,
    "referred_reward": 40
  }
}
```

---

### 3. Apply Referral Code
Apply a referral code to earn rewards (for new users).

**Endpoint**: `POST /api/mobile/referral/apply`

**Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body**:
```json
{
  "referral_code": "REF123ABC"
}
```

**Success Response** (200):
```json
{
  "success": true,
  "message": "Referral code applied successfully! You earned 40 credits",
  "data": {
    "reward": {
      "uid": "uuid-here",
      "referrer_user_uid": "referrer-uid",
      "referred_user_uid": "your-uid",
      "referrer_credits_earned": 100,
      "referred_credits_earned": 40,
      "created_at": "2026-04-30T12:00:00.000000Z"
    },
    "credits_earned": 40
  }
}
```

**Error Responses**:

Already Used (400):
```json
{
  "success": false,
  "message": "You have already used a referral code"
}
```

Invalid Code (404):
```json
{
  "success": false,
  "message": "Invalid referral code"
}
```

Own Code (400):
```json
{
  "success": false,
  "message": "You cannot use your own referral code"
}
```

---

### 4. Get Referral Statistics
Get statistics about your referrals (how many people you referred).

**Endpoint**: `GET /api/mobile/referral/statistics`

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response** (200):
```json
{
  "success": true,
  "message": "Referral statistics retrieved successfully",
  "data": {
    "my_referral_code": "REF123ABC",
    "total_referrals": 5,
    "total_bonus_earned": 500,
    "referrals": [
      {
        "email": "user1@example.com",
        "name": "User One",
        "date": "2026-04-25",
        "credits_earned": 100
      },
      {
        "email": "user2@example.com",
        "name": "User Two",
        "date": "2026-04-26",
        "credits_earned": 100
      }
    ]
  }
}
```

---

### 5. Get Referral History (Paginated)
Get detailed history of all users you referred with pagination.

**Endpoint**: `GET /api/mobile/referral/history`

**Headers**:
```
Authorization: Bearer {token}
```

**Query Parameters**:
- `per_page` (optional): Number of items per page (default: 15)

**Success Response** (200):
```json
{
  "success": true,
  "message": "Referral history retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "uid": "reward-uid-1",
        "referrer_user_uid": "your-uid",
        "referred_user_uid": "user-uid-1",
        "referrer_credits_earned": 100,
        "referred_credits_earned": 40,
        "created_at": "2026-04-25T10:30:00.000000Z",
        "referred": {
          "uid": "user-uid-1",
          "name": "User One",
          "email": "user1@example.com",
          "created_at": "2026-04-25T10:00:00.000000Z"
        }
      }
    ],
    "first_page_url": "http://api.example.com/api/mobile/referral/history?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://api.example.com/api/mobile/referral/history?page=1",
    "next_page_url": null,
    "path": "http://api.example.com/api/mobile/referral/history",
    "per_page": 15,
    "prev_page_url": null,
    "to": 5,
    "total": 5
  }
}
```

---

### 6. Get My Referrer Information
Check if you were referred by someone and see their information.

**Endpoint**: `GET /api/mobile/referral/my-referrer`

**Headers**:
```
Authorization: Bearer {token}
```

**Success Response - Has Referrer** (200):
```json
{
  "success": true,
  "message": "Referrer information retrieved successfully",
  "data": {
    "has_referrer": true,
    "referrer": {
      "name": "John Doe",
      "email": "john@example.com"
    },
    "credits_earned": 40,
    "date": "2026-04-20"
  }
}
```

**Success Response - No Referrer** (200):
```json
{
  "success": true,
  "message": "No referrer found",
  "data": {
    "has_referrer": false,
    "message": "You were not referred by anyone"
  }
}
```

---

## Usage Flow

### For New Users (Being Referred)

1. **During Registration** (Optional):
   - User can validate referral code first: `POST /api/mobile/referral/validate`
   - If valid, proceed with registration

2. **After Registration**:
   - User applies referral code: `POST /api/mobile/referral/apply`
   - Both users receive credits automatically

3. **Check Referrer**:
   - User can check who referred them: `GET /api/mobile/referral/my-referrer`

### For Existing Users (Referring Others)

1. **Get Your Code**:
   - Get your referral code: `GET /api/mobile/referral/my-code`
   - Share this code with friends

2. **Track Referrals**:
   - Check statistics: `GET /api/mobile/referral/statistics`
   - View detailed history: `GET /api/mobile/referral/history`

---

## Business Rules

1. **One-Time Use**: Each user can only use one referral code
2. **No Self-Referral**: Users cannot use their own referral code
3. **Automatic Rewards**: Credits are added automatically when referral is applied
4. **Unique Codes**: Each user has a unique referral code (format: REF + 6 random characters)
5. **Tracking**: All referrals are tracked in the `referral_rewards` table

---

## Database Schema

### referral_rewards Table
```sql
CREATE TABLE referral_rewards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uid VARCHAR(36) UNIQUE NOT NULL,
    referrer_user_uid VARCHAR(36) NOT NULL,
    referred_user_uid VARCHAR(36) NOT NULL,
    referrer_credits_earned INT DEFAULT 100,
    referred_credits_earned INT DEFAULT 40,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (referrer_user_uid) REFERENCES users(uid) ON DELETE CASCADE,
    FOREIGN KEY (referred_user_uid) REFERENCES users(uid) ON DELETE CASCADE,
    UNIQUE KEY unique_referral (referrer_user_uid, referred_user_uid)
);
```

---

## Error Codes

| Code | Message | Description |
|------|---------|-------------|
| 200 | Success | Request successful |
| 400 | Bad Request | Invalid input or business rule violation |
| 404 | Not Found | Referral code not found |
| 422 | Validation Error | Input validation failed |
| 500 | Server Error | Internal server error |

---

## Testing Examples

### Using cURL

**Validate Referral Code**:
```bash
curl -X POST http://localhost/api/mobile/referral/validate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"referral_code": "REF123ABC"}'
```

**Get My Code**:
```bash
curl -X GET http://localhost/api/mobile/referral/my-code \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Apply Referral Code**:
```bash
curl -X POST http://localhost/api/mobile/referral/apply \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"referral_code": "REF123ABC"}'
```

**Get Statistics**:
```bash
curl -X GET http://localhost/api/mobile/referral/statistics \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Get History**:
```bash
curl -X GET "http://localhost/api/mobile/referral/history?per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Get My Referrer**:
```bash
curl -X GET http://localhost/api/mobile/referral/my-referrer \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Integration with Registration

The referral system can be integrated with the registration process:

```javascript
// Step 1: Validate referral code (optional)
const validateResponse = await fetch('/api/mobile/referral/validate', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ referral_code: 'REF123ABC' })
});

// Step 2: If valid, show referrer info to user
if (validateResponse.data.valid) {
  console.log(`You will be referred by ${validateResponse.data.referrer_name}`);
}

// Step 3: After registration, apply referral code
const applyResponse = await fetch('/api/mobile/referral/apply', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ referral_code: 'REF123ABC' })
});

// Step 4: Show success message
if (applyResponse.success) {
  console.log(`You earned ${applyResponse.data.credits_earned} credits!`);
}
```

---

## Summary

Total Referral Endpoints: **6**

1. ✅ `POST /api/mobile/referral/validate` - Validate referral code
2. ✅ `GET /api/mobile/referral/my-code` - Get my referral code
3. ✅ `POST /api/mobile/referral/apply` - Apply referral code
4. ✅ `GET /api/mobile/referral/statistics` - Get referral statistics
5. ✅ `GET /api/mobile/referral/history` - Get referral history (paginated)
6. ✅ `GET /api/mobile/referral/my-referrer` - Get my referrer info

All endpoints are protected with `auth:sanctum` middleware and require a valid Bearer token.
