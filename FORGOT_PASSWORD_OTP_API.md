# Forgot Password with OTP API Documentation

Sistem forgot password menggunakan OTP (One-Time Password) untuk reset password dengan aman.

## Overview

Flow forgot password:
1. **Request Reset**: User input email untuk request reset password
2. **Send OTP**: System kirim OTP 6 digit ke email (berlaku 15 menit)
3. **Verify OTP**: User input OTP untuk verifikasi
4. **Reset Password**: User input password baru setelah OTP terverifikasi

---

## Security Features

- **OTP Expiry**: 15 menit (lebih pendek dari email verification)
- **Single Use**: OTP dihapus setelah berhasil digunakan
- **Token Revocation**: Semua token user di-revoke setelah reset password
- **Email Validation**: Validasi email sebelum mengirim OTP
- **Rate Limiting**: Bisa ditambahkan untuk mencegah abuse

---

## Endpoints

### 1. Request Password Reset
**POST** `/api/mobile/auth/forgot-password`

Mengirim OTP ke email user untuk reset password.

**Request Body:**
```json
{
  "email": "user@example.com"
}
```

**Validation Rules:**
- `email`: required, email format

**Response Success:**
```json
{
  "success": true,
  "message": "Password reset code sent to your email",
  "data": null
}
```

**Response Error - User Not Found:**
```json
{
  "success": false,
  "message": "User with this email not found"
}
```

### 2. Verify Reset OTP
**POST** `/api/mobile/auth/verify-reset-otp`

Verifikasi OTP sebelum reset password (optional step untuk UX).

**Request Body:**
```json
{
  "email": "user@example.com",
  "otp": "123456"
}
```

**Validation Rules:**
- `email`: required, email format
- `otp`: required, string, exactly 6 characters

**Response Success:**
```json
{
  "success": true,
  "message": "Reset code verified successfully",
  "data": null
}
```

**Response Error - Invalid OTP:**
```json
{
  "success": false,
  "message": "Invalid reset code"
}
```

**Response Error - Expired OTP:**
```json
{
  "success": false,
  "message": "Reset code has expired. Please request a new one."
}
```

### 3. Reset Password
**POST** `/api/mobile/auth/reset-password`

Reset password menggunakan OTP yang valid.

**Request Body:**
```json
{
  "email": "user@example.com",
  "otp": "123456",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```

**Validation Rules:**
- `email`: required, email format
- `otp`: required, string, exactly 6 characters
- `new_password`: required, string, min:8, confirmed

**Response Success:**
```json
{
  "success": true,
  "message": "Password reset successfully. Please login with your new password.",
  "data": null
}
```

**Response Error - Invalid OTP:**
```json
{
  "success": false,
  "message": "Invalid reset code"
}
```

---

## Email Template

### HTML Email Features
- 🔐 Security-focused design
- ⏰ Clear expiry time (15 minutes)
- 🛡️ Security notices and warnings
- 📱 Mobile-responsive design
- ⚠️ Warning for unauthorized requests

### Email Content
- **Subject**: "Password Reset - JapanAI"
- **OTP Display**: Large, easy-to-read 6-digit code
- **Expiry Notice**: Clear 15-minute expiry warning
- **Security Tips**: Guidelines for safe usage
- **Support Contact**: Help email for assistance

---

## Database Fields

Field baru yang ditambahkan ke tabel `users`:

```sql
password_reset_otp VARCHAR(6) NULL
password_reset_otp_expires_at TIMESTAMP NULL
```

---

## Flow Diagram

```
1. User Forgot Password
   ↓
2. Input Email
   ↓
3. Generate 6-digit OTP
   ↓
4. Save OTP + 15min Expiry to Database
   ↓
5. Send Email with OTP
   ↓
6. User Input OTP + New Password
   ↓
7. Verify OTP & Expiry
   ↓
8. Update Password
   ↓
9. Clear OTP from Database
   ↓
10. Revoke All User Tokens
   ↓
11. User Must Login Again
```

---

## Security Considerations

### OTP Security
- **Shorter Expiry**: 15 menit (vs 10 menit untuk email verification)
- **Single Use**: OTP dihapus setelah digunakan
- **Secure Generation**: Menggunakan `random_int()` untuk generate OTP
- **No Reuse**: Setiap request generate OTP baru

### Password Security
- **Token Revocation**: Semua token di-revoke setelah reset
- **Force Re-login**: User harus login ulang dengan password baru
- **Hash Update**: Password di-hash dengan bcrypt

### Email Security
- **Clear Instructions**: Email berisi instruksi yang jelas
- **Warning Messages**: Peringatan jika tidak request reset
- **No Sensitive Data**: Email tidak berisi data sensitif lainnya

---

## Error Handling

### Validation Errors
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "otp": ["The otp field must be 6 characters."],
    "new_password": ["The new password field must be at least 8 characters."]
  }
}
```

### System Errors
```json
{
  "success": false,
  "message": "Failed to send reset code: SMTP connection failed"
}
```

---

## Usage Examples

### 1. Request Password Reset
```bash
curl -X POST /api/mobile/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com"}'
```

### 2. Verify Reset OTP (Optional)
```bash
curl -X POST /api/mobile/auth/verify-reset-otp \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "otp": "123456"}'
```

### 3. Reset Password
```bash
curl -X POST /api/mobile/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "otp": "123456",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
  }'
```

---

## Mobile App Implementation

### Recommended UX Flow

#### Step 1: Forgot Password Screen
```javascript
const handleForgotPassword = async (email) => {
  try {
    const response = await api.post('/auth/forgot-password', { email });
    // Navigate to OTP verification screen
    navigation.navigate('VerifyResetOTP', { email });
  } catch (error) {
    showError(error.message);
  }
};
```

#### Step 2: OTP Verification Screen
```javascript
const handleVerifyOTP = async (email, otp) => {
  try {
    const response = await api.post('/auth/verify-reset-otp', { email, otp });
    // Navigate to new password screen
    navigation.navigate('NewPassword', { email, otp });
  } catch (error) {
    showError(error.message);
  }
};
```

#### Step 3: New Password Screen
```javascript
const handleResetPassword = async (email, otp, newPassword, confirmPassword) => {
  try {
    const response = await api.post('/auth/reset-password', {
      email,
      otp,
      new_password: newPassword,
      new_password_confirmation: confirmPassword
    });
    
    // Show success message and navigate to login
    showSuccess('Password reset successfully');
    navigation.navigate('Login');
  } catch (error) {
    showError(error.message);
  }
};
```

---

## Testing with Mailtrap

Untuk testing, gunakan Mailtrap inbox:
1. User request forgot password
2. Check Mailtrap inbox untuk email OTP
3. Copy 6-digit OTP code
4. Test verify OTP endpoint
5. Test reset password endpoint

---

## Status Codes

- `200 OK`: Request berhasil
- `400 Bad Request`: Validation error atau invalid OTP
- `404 Not Found`: User tidak ditemukan
- `500 Internal Server Error`: Server error

---

## Rate Limiting Recommendations

Untuk production, implementasi rate limiting:

```php
// Limit forgot password requests
Route::middleware('throttle:5,60')->group(function () {
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
});

// Limit OTP verification attempts
Route::middleware('throttle:10,60')->group(function () {
    Route::post('/verify-reset-otp', [AuthController::class, 'verifyResetOTP']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});
```

---

## Monitoring & Logging

### Metrics to Track
- Forgot password request rate
- OTP verification success rate
- Password reset completion rate
- Failed attempts and potential abuse

### Important Logs
```
[INFO] Password reset OTP sent to: user@example.com
[INFO] Password reset OTP verified for: user@example.com
[INFO] Password reset completed for: user@example.com
[WARNING] Invalid OTP attempt for: user@example.com
[ERROR] Failed to send reset OTP: SMTP error
```