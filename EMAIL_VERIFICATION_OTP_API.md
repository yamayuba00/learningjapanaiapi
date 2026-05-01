# Email Verification with OTP API Documentation

Sistem verifikasi email menggunakan OTP (One-Time Password) untuk registrasi user baru.

## Overview

Setelah registrasi, user akan menerima email berisi kode OTP 6 digit yang berlaku selama 10 menit. User harus memasukkan kode OTP untuk memverifikasi email mereka.

---

## Endpoints

### 1. Register User
**POST** `/api/mobile/auth/register`

Registrasi user baru dan mengirim OTP ke email.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone_number": "+6281234567890",
  "instagram": "@johndoe"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Registration successful. Please check your email for the verification code.",
  "data": {
    "user": {
      "uid": "uuid",
      "name": "John Doe",
      "email": "john@example.com",
      "phone_number": "+6281234567890",
      "instagram": "@johndoe",
      "referal_code": "REF123456",
      "referal_by_code": "SYSTEM",
      "email_verified_at": null,
      "email_verification_sent_at": "2026-04-30T16:00:00.000000Z",
      "created_at": "2026-04-30T16:00:00.000000Z",
      "updated_at": "2026-04-30T16:00:00.000000Z"
    }
  }
}
```

### 2. Verify Email with OTP
**POST** `/api/mobile/auth/verify-email-otp`

Verifikasi email menggunakan kode OTP yang diterima via email.

**Request Body:**
```json
{
  "email": "john@example.com",
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
  "message": "Email verified successfully",
  "data": null
}
```

**Response Error - Invalid OTP:**
```json
{
  "success": false,
  "message": "Invalid verification code"
}
```

**Response Error - Expired OTP:**
```json
{
  "success": false,
  "message": "Verification code has expired. Please request a new one."
}
```

**Response Error - Already Verified:**
```json
{
  "success": false,
  "message": "Email already verified"
}
```

### 3. Resend Verification OTP
**POST** `/api/mobile/auth/resend-verification`

Mengirim ulang kode OTP ke email user.

**Request Body:**
```json
{
  "email": "john@example.com"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Verification code sent successfully",
  "data": null
}
```

---

## Email Template

### HTML Email
Email yang dikirim menggunakan template HTML yang menarik dengan:
- Logo dan branding aplikasi
- Kode OTP yang besar dan mudah dibaca
- Informasi kedaluwarsa (10 menit)
- Peringatan keamanan
- Footer dengan informasi kontak

### Text Email
Tersedia juga versi text untuk email client yang tidak mendukung HTML.

---

## OTP Specifications

- **Format**: 6 digit angka (000000 - 999999)
- **Validity**: 10 menit dari waktu pengiriman
- **Storage**: Disimpan di database dalam field `email_verification_otp`
- **Expiry**: Disimpan di field `email_verification_otp_expires_at`
- **Security**: OTP dihapus setelah berhasil diverifikasi

---

## Database Fields

Field baru yang ditambahkan ke tabel `users`:

```sql
email_verification_otp VARCHAR(6) NULL
email_verification_otp_expires_at TIMESTAMP NULL
```

---

## Email Configuration

Menggunakan Mailtrap untuk testing:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=a30ba8118741e5
MAIL_PASSWORD=2310c63c67229e
MAIL_FROM_ADDRESS="learning@japanai.com"
MAIL_FROM_NAME="JapanAI"
```

---

## Flow Diagram

```
1. User Register
   ↓
2. Generate 6-digit OTP
   ↓
3. Save OTP + Expiry to Database
   ↓
4. Send Email with OTP
   ↓
5. User Input OTP in App
   ↓
6. Verify OTP & Expiry
   ↓
7. Mark Email as Verified
   ↓
8. Clear OTP from Database
```

---

## Error Handling

### Validation Errors
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "otp": ["The otp field must be 6 characters."]
  }
}
```

### System Errors
```json
{
  "success": false,
  "message": "Email verification failed: Database connection error"
}
```

---

## Security Features

1. **OTP Expiry**: Kode OTP otomatis kedaluwarsa setelah 10 menit
2. **Single Use**: OTP dihapus setelah berhasil digunakan
3. **Rate Limiting**: Bisa ditambahkan untuk mencegah spam
4. **Secure Generation**: Menggunakan `random_int()` untuk generate OTP
5. **Email Validation**: Validasi format email sebelum mengirim OTP

---

## Testing

Untuk testing, gunakan Mailtrap inbox untuk melihat email yang dikirim:
- Login ke Mailtrap dashboard
- Buka inbox untuk melihat email OTP
- Copy kode OTP untuk testing verifikasi

---

## Production Considerations

1. **Email Provider**: Ganti dengan provider email production (SendGrid, AWS SES, dll)
2. **Rate Limiting**: Implementasi rate limiting untuk prevent abuse
3. **Monitoring**: Monitor email delivery success rate
4. **Backup Method**: Sediakan alternatif verifikasi (SMS, WhatsApp)
5. **Logging**: Log semua aktivitas verifikasi untuk audit