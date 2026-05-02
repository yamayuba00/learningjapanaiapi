# User Profile API Documentation

API untuk mendapatkan dan mengupdate profil user dengan response yang clean dan optimized untuk mobile app.

## Get User Profile

### Endpoint
**GET** `/api/mobile/auth/profile`

### Headers
```
Authorization: Bearer {token}
```

### Response Success (Clean & Optimized)
```json
{
  "success": true,
  "message": "Profile retrieved successfully",
  "data": {
    "uid": "0c1e734d-be70-41d3-b29b-35135da022b9",
    "name": "bayupm",
    "email": "bayupriyambada76@gmail.com",
    "phone_number": "081293005411",
    "instagram": "bpriyambadam",
    "avatar_url": null,
    "referal_code": "REFTWLMDQ",
    "email_verified": true,
    "is_blocked": false,
    "member_since": "2026-05-02",
    "credits": {
      "current": 40,
      "total_earned": 40,
      "streak": 0
    }
  }
}
```

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `uid` | string | Unique user identifier |
| `name` | string | User's display name |
| `email` | string | User's email address |
| `phone_number` | string/null | User's phone number |
| `instagram` | string/null | Instagram username |
| `avatar_url` | string/null | Profile picture URL |
| `referal_code` | string | User's referral code |
| `email_verified` | boolean | Email verification status |
| `is_blocked` | boolean | Account blocked status |
| `member_since` | string | Registration date (Y-m-d format) |
| `credits.current` | integer | Current available credits |
| `credits.total_earned` | integer | Total points earned lifetime |
| `credits.streak` | integer | Current streak count |

---

## Update User Profile

### Endpoint
**PUT** `/api/mobile/auth/profile`

### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

### Request Body
```json
{
  "name": "New Name",
  "phone_number": "+6281234567890",
  "instagram": "@newusername",
  "avatar_url": "https://example.com/avatar.jpg"
}
```

### Validation Rules
- `name`: sometimes, string, max:255
- `phone_number`: nullable, string, max:20
- `instagram`: nullable, string, max:100
- `avatar_url`: nullable, url

### Response Success
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "uid": "0c1e734d-be70-41d3-b29b-35135da022b9",
    "name": "New Name",
    "email": "bayupriyambada76@gmail.com",
    "phone_number": "+6281234567890",
    "instagram": "@newusername",
    "avatar_url": "https://example.com/avatar.jpg",
    "referal_code": "REFTWLMDQ",
    "email_verified": true,
    "is_blocked": false,
    "member_since": "2026-05-02",
    "credits": {
      "current": 40,
      "total_earned": 40,
      "streak": 0
    }
  }
}
```

---

## Comparison: Old vs New Response

### ❌ Old Response (Too Verbose)
```json
{
  "success": true,
  "message": "Profile retrieved successfully",
  "data": {
    "success": true,
    "user": {
      "id": 25,
      "uid": "0c1e734d-be70-41d3-b29b-35135da022b9",
      "name": "bayupm",
      "email": "bayupriyambada76@gmail.com",
      "phone_number": "081293005411",
      "instagram": "bpriyambadam",
      "avatar_url": null,
      "referal_code": "REFTWLMDQ",
      "referal_by_code": "REFP6Z9PD",
      "email_verified_at": "2026-05-02T17:37:08.000000Z",
      "email_verification_otp": null,
      "email_verification_otp_expires_at": null,
      "password_reset_otp": null,
      "password_reset_otp_expires_at": null,
      "email_verification_sent_at": "2026-05-02T17:36:49.000000Z",
      "last_login": "2026-05-02T18:13:09.000000Z",
      "is_blocked": false,
      "blocked_at": null,
      "blocked_reason": null,
      "created_at": "2026-05-02T17:48:02.000000Z",
      "updated_at": "2026-05-02T18:13:09.000000Z",
      "credit": {
        "id": 25,
        "uid": "a2a7728d-3147-4702-8061-59968acbb9ee",
        "user_uid": "0c1e734d-be70-41d3-b29b-35135da022b9",
        "user_id": "25",
        "credits": 40,
        "total_points": 40,
        "streak": 0,
        "cycle_number": 1,
        "cycle_start_date": "2026-05-02T00:00:00.000000Z",
        "last_claim_date": null,
        "created_at": "2026-05-02T17:48:02.000000Z",
        "updated_at": "2026-05-02T18:13:09.000000Z"
      }
    }
  }
}
```

### ✅ New Response (Clean & Optimized)
```json
{
  "success": true,
  "message": "Profile retrieved successfully",
  "data": {
    "uid": "0c1e734d-be70-41d3-b29b-35135da022b9",
    "name": "bayupm",
    "email": "bayupriyambada76@gmail.com",
    "phone_number": "081293005411",
    "instagram": "bpriyambadam",
    "avatar_url": null,
    "referal_code": "REFTWLMDQ",
    "email_verified": true,
    "is_blocked": false,
    "member_since": "2026-05-02",
    "credits": {
      "current": 40,
      "total_earned": 40,
      "streak": 0
    }
  }
}
```

---

## Benefits of New Response

### 🚀 **Performance**
- **75% smaller** response size
- Faster parsing on mobile devices
- Reduced bandwidth usage

### 🎯 **Mobile-Optimized**
- Only essential fields for UI
- Simplified credit information
- Boolean flags instead of timestamps
- Clean date format

### 🔒 **Security**
- No sensitive internal IDs
- No OTP or token information
- No database timestamps
- No referral tracking data

### 📱 **Developer Experience**
- Easier to parse and use
- Consistent field naming
- Logical data grouping
- Clear boolean values

---

## Mobile App Usage

### React Native Example
```javascript
const ProfileScreen = () => {
  const [profile, setProfile] = useState(null);
  
  const fetchProfile = async () => {
    try {
      const response = await api.get('/auth/profile');
      const profileData = response.data;
      
      // Easy to use - no nested objects
      setProfile(profileData);
      
      // Direct access to clean data
      console.log('Credits:', profileData.credits.current);
      console.log('Verified:', profileData.email_verified);
      console.log('Member since:', profileData.member_since);
      
    } catch (error) {
      console.error('Failed to fetch profile:', error);
    }
  };
  
  return (
    <View>
      <Text>{profile?.name}</Text>
      <Text>{profile?.email}</Text>
      <Text>Credits: {profile?.credits.current}</Text>
      <Text>Referral Code: {profile?.referal_code}</Text>
      {profile?.email_verified && <Badge>Verified</Badge>}
    </View>
  );
};
```

### Flutter Example
```dart
class ProfileModel {
  final String uid;
  final String name;
  final String email;
  final String? phoneNumber;
  final String? instagram;
  final String? avatarUrl;
  final String referalCode;
  final bool emailVerified;
  final bool isBlocked;
  final String memberSince;
  final Credits credits;
  
  ProfileModel.fromJson(Map<String, dynamic> json)
    : uid = json['uid'],
      name = json['name'],
      email = json['email'],
      phoneNumber = json['phone_number'],
      instagram = json['instagram'],
      avatarUrl = json['avatar_url'],
      referalCode = json['referal_code'],
      emailVerified = json['email_verified'],
      isBlocked = json['is_blocked'],
      memberSince = json['member_since'],
      credits = Credits.fromJson(json['credits']);
}

class Credits {
  final int current;
  final int totalEarned;
  final int streak;
  
  Credits.fromJson(Map<String, dynamic> json)
    : current = json['current'],
      totalEarned = json['total_earned'],
      streak = json['streak'];
}
```

---

## Error Handling

### User Not Found
```json
{
  "success": false,
  "message": "User not found"
}
```

### Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### Validation Error (Update Profile)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "name": ["The name field must not be greater than 255 characters."],
    "avatar_url": ["The avatar url format is invalid."]
  }
}
```

---

## Status Codes

- `200 OK`: Profile retrieved/updated successfully
- `400 Bad Request`: Validation error
- `401 Unauthorized`: Invalid or missing token
- `404 Not Found`: User not found
- `500 Internal Server Error`: Server error

---

## Testing

### Get Profile
```bash
curl -X GET /api/mobile/auth/profile \
  -H "Authorization: Bearer {token}"
```

### Update Profile
```bash
curl -X PUT /api/mobile/auth/profile \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Name",
    "phone_number": "+6281234567890",
    "instagram": "@newusername"
  }'
```