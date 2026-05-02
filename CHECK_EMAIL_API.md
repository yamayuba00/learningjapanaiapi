# Check Email API Documentation

Endpoint untuk validasi apakah email sudah terdaftar atau belum dalam sistem.

## Overview

API ini berguna untuk UX yang lebih baik di mobile app, memungkinkan user untuk:
- Mengecek ketersediaan email sebelum registrasi
- Mengetahui status verifikasi email
- Mendapatkan informasi dasar user jika email sudah terdaftar

---

## Endpoint

### Check Email Availability
**POST** `/api/mobile/auth/check-email`

Mengecek apakah email sudah terdaftar dalam sistem dan status verifikasinya.

**Request Body:**
```json
{
  "email": "user@example.com"
}
```

**Validation Rules:**
- `email`: required, email format

---

## Response Examples

### 1. Email Available (Belum Terdaftar)
```json
{
  "success": true,
  "message": "Email check completed",
  "data": {
    "success": true,
    "message": "Email is available for registration"
  }
}
```

### 2. Email Already Registered & Verified
```json
{
  "success": true,
  "message": "Email check completed",
  "data": {
    "success": true,
    "message": "Email is already registered and verified"
  }
}
```

### 3. Email Already Registered but Not Verified
```json
{
  "success": true,
  "message": "Email check completed",
  "data": {
    "success": true,
    "message": "Email is already registered but not verified"
  }
}
```

---

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | Status keberhasilan request |
| `message` | string | Pesan deskriptif status email |

---

## Message Types

| Message | Meaning |
|---------|---------|
| `"Email is available for registration"` | Email belum terdaftar, bisa digunakan untuk registrasi |
| `"Email is already registered and verified"` | Email sudah terdaftar dan terverifikasi, bisa login |
| `"Email is already registered but not verified"` | Email sudah terdaftar tapi belum verifikasi |

---

## Use Cases

### 1. Registration Flow
```javascript
// Check email sebelum registrasi
const checkEmail = async (email) => {
  const response = await api.post('/auth/check-email', { email });
  const message = response.data.message;
  
  if (message === 'Email is already registered and verified') {
    // Email sudah terdaftar dan verified
    showError('Email already registered. Please login instead.');
    navigateToLogin();
  } else if (message === 'Email is already registered but not verified') {
    // Email terdaftar tapi belum verified
    showError('Email registered but not verified. Please verify your email.');
    navigateToVerification(email);
  } else if (message === 'Email is available for registration') {
    // Email available, lanjut registrasi
    proceedWithRegistration(email);
  }
};
```

### 2. Login Flow
```javascript
// Check email sebelum login
const checkEmailBeforeLogin = async (email) => {
  const response = await api.post('/auth/check-email', { email });
  const message = response.data.message;
  
  if (message === 'Email is available for registration') {
    showError('Email not registered. Please register first.');
    navigateToRegister();
  } else if (message === 'Email is already registered but not verified') {
    showWarning('Email not verified. Please verify your email first.');
    // Masih bisa login, tapi tampilkan warning
  }
  // Lanjut ke login form
};
```

### 3. Forgot Password Flow
```javascript
// Check email sebelum forgot password
const checkEmailBeforeForgotPassword = async (email) => {
  const response = await api.post('/auth/check-email', { email });
  const message = response.data.message;
  
  if (message === 'Email is available for registration') {
    showError('Email not found in our system.');
    return false;
  }
  
  // Email exists, lanjut kirim reset OTP
  return true;
};
```

---

## Error Handling

### Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### System Error
```json
{
  "success": false,
  "message": "Failed to check email: Database connection error"
}
```

---

## Security Considerations

### Information Disclosure
- API ini memberikan informasi apakah email terdaftar atau tidak
- Ini bisa digunakan untuk email enumeration attack
- Pertimbangkan rate limiting untuk mencegah abuse

### Rate Limiting Recommendation
```php
// Limit check email requests
Route::middleware('throttle:10,60')->group(function () {
    Route::post('/check-email', [AuthController::class, 'checkEmail']);
});
```

### Privacy Protection
- Hanya menampilkan informasi minimal (nama, email, status verifikasi)
- Tidak menampilkan data sensitif lainnya
- Tidak menampilkan password hash atau token

---

## Mobile App Implementation

### React Native Example
```javascript
import { useState } from 'react';

const useEmailCheck = () => {
  const [isChecking, setIsChecking] = useState(false);
  
  const checkEmail = async (email) => {
    setIsChecking(true);
    try {
      const response = await fetch('/api/mobile/auth/check-email', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email }),
      });
      
      const result = await response.json();
      return result.data;
    } catch (error) {
      throw new Error('Failed to check email');
    } finally {
      setIsChecking(false);
    }
  };
  
  return { checkEmail, isChecking };
};

// Usage in component
const RegistrationScreen = () => {
  const { checkEmail } = useEmailCheck();
  
  const handleEmailBlur = async (email) => {
    if (email) {
      const result = await checkEmail(email);
      if (result.exists) {
        setEmailError('Email already registered');
      } else {
        setEmailError(null);
      }
    }
  };
  
  return (
    <TextInput
      placeholder="Email"
      onBlur={(e) => handleEmailBlur(e.target.value)}
    />
  );
};
```

---

## Testing

### Test Cases

1. **Valid Email - Available**
   ```bash
   curl -X POST /api/mobile/auth/check-email \
     -H "Content-Type: application/json" \
     -d '{"email": "new@example.com"}'
   ```

2. **Valid Email - Already Registered**
   ```bash
   curl -X POST /api/mobile/auth/check-email \
     -H "Content-Type: application/json" \
     -d '{"email": "existing@example.com"}'
   ```

3. **Invalid Email Format**
   ```bash
   curl -X POST /api/mobile/auth/check-email \
     -H "Content-Type: application/json" \
     -d '{"email": "invalid-email"}'
   ```

4. **Missing Email**
   ```bash
   curl -X POST /api/mobile/auth/check-email \
     -H "Content-Type: application/json" \
     -d '{}'
   ```

---

## Performance Considerations

### Database Optimization
- Index pada kolom `email` untuk query yang cepat
- Gunakan `select` untuk membatasi kolom yang diambil
- Cache hasil untuk email yang sering dicek

### Response Optimization
```php
// Optimized query - hanya ambil kolom yang diperlukan
$user = User::where('email', $email)
    ->select(['name', 'email', 'email_verified_at', 'created_at'])
    ->first();
```

---

## Status Codes

- `200 OK`: Request berhasil
- `400 Bad Request`: Validation error
- `500 Internal Server Error`: Server error

---

## Monitoring

### Metrics to Track
- Email check request rate
- Ratio existing vs new emails
- Response time performance
- Error rate

### Important Logs
```
[INFO] Email check request: user@example.com - exists: true, verified: true
[INFO] Email check request: new@example.com - exists: false
[WARNING] High frequency email check from IP: 192.168.1.1
[ERROR] Email check failed: Database timeout
```