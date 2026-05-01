# Partnership API Documentation

API untuk mengelola partnership JLPT classes dan internships di mobile app.

## Base URL
```
/api/mobile/partnership
```

## Authentication
Semua endpoint memerlukan authentication dengan Bearer token.

---

## JLPT Classes Endpoints

### 1. Get All JLPT Classes
**GET** `/jlpt-classes`

Mendapatkan daftar semua JLPT classes yang aktif dan terverifikasi.

**Response:**
```json
{
  "success": true,
  "message": "JLPT classes retrieved successfully",
  "data": [
    {
      "uid": "uuid",
      "name": "Nihongo Center Jakarta",
      "description": "Lembaga kursus bahasa Jepang terpercaya...",
      "logo_url": "https://example.com/logos/nihongo-center.png",
      "website": "https://nihongocenter.co.id",
      "referral_code": "NIHONGO2024",
      "programs": [
        "JLPT N5 Preparation Course",
        "JLPT N4 Preparation Course",
        "..."
      ],
      "contact_whatsapp": "+6281234567890",
      "contact_instagram": "@nihongocenter_jkt",
      "display_order": 1,
      "created_at": "2026-04-29T18:00:00.000000Z",
      "updated_at": "2026-04-29T18:00:00.000000Z"
    }
  ]
}
```

### 2. Get JLPT Class Details
**GET** `/jlpt-classes/{uid}`

Mendapatkan detail JLPT class berdasarkan UID.

**Parameters:**
- `uid` (string, required): UID dari JLPT class

**Response:**
```json
{
  "success": true,
  "message": "JLPT class details retrieved successfully",
  "data": {
    "uid": "uuid",
    "name": "Nihongo Center Jakarta",
    "description": "Lembaga kursus bahasa Jepang terpercaya...",
    "logo_url": "https://example.com/logos/nihongo-center.png",
    "website": "https://nihongocenter.co.id",
    "referral_code": "NIHONGO2024",
    "programs": [
      "JLPT N5 Preparation Course",
      "JLPT N4 Preparation Course",
      "JLPT N3 Preparation Course",
      "JLPT N2 Preparation Course",
      "JLPT N1 Preparation Course",
      "Business Japanese",
      "Conversation Class"
    ],
    "contact_whatsapp": "+6281234567890",
    "contact_instagram": "@nihongocenter_jkt",
    "display_order": 1,
    "created_at": "2026-04-29T18:00:00.000000Z",
    "updated_at": "2026-04-29T18:00:00.000000Z"
  }
}
```

### 3. Submit JLPT Class Inquiry
**POST** `/jlpt-classes/inquire`

Mengirim inquiry untuk JLPT class.

**Request Body:**
```json
{
  "partner_uid": "uuid",
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+6281234567890",
  "message": "Saya tertarik dengan program N3 preparation course"
}
```

**Validation Rules:**
- `partner_uid`: required, string
- `name`: required, string, max:255
- `email`: required, email, max:255
- `phone`: required, string, max:20
- `message`: nullable, string, max:1000

**Response:**
```json
{
  "success": true,
  "message": "JLPT class inquiry submitted successfully",
  "data": {
    "inquiry": {
      "uid": "uuid",
      "user_uid": "user-uuid",
      "type": "jlpt_class",
      "partner_uid": "partner-uuid",
      "partner_name": "Nihongo Center Jakarta",
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+6281234567890",
      "message": "Saya tertarik dengan program N3 preparation course",
      "status": "pending",
      "submitted_at": "2026-04-29T18:00:00.000000Z",
      "created_at": "2026-04-29T18:00:00.000000Z",
      "updated_at": "2026-04-29T18:00:00.000000Z"
    }
  }
}
```

---

## Internships Endpoints

### 4. Get All Internships
**GET** `/internships`

Mendapatkan daftar semua internships yang aktif dan terverifikasi.

**Response:**
```json
{
  "success": true,
  "message": "Internships retrieved successfully",
  "data": [
    {
      "uid": "uuid",
      "name": "Tokyo Tech Solutions",
      "description": "Perusahaan teknologi Jepang yang menawarkan program magang...",
      "logo_url": "https://example.com/logos/tokyo-tech.png",
      "website": "https://tokyotech.jp",
      "programs": [
        "Software Engineer Internship",
        "Data Analyst Internship",
        "UI/UX Designer Internship",
        "Project Manager Trainee"
      ],
      "benefits": [
        "Gaji kompetitif (¥200,000 - ¥300,000/bulan)",
        "Akomodasi disediakan",
        "Asuransi kesehatan",
        "Pelatihan bahasa Jepang gratis",
        "Sertifikat internasional",
        "Kesempatan kerja full-time"
      ],
      "contact_whatsapp": "+6281234567893",
      "contact_instagram": "@tokyotech_careers",
      "total_alumni": 150,
      "success_rate": 92.50,
      "display_order": 1,
      "created_at": "2026-04-29T18:00:00.000000Z",
      "updated_at": "2026-04-29T18:00:00.000000Z"
    }
  ]
}
```

### 5. Get Internship Details
**GET** `/internships/{uid}`

Mendapatkan detail internship berdasarkan UID.

**Parameters:**
- `uid` (string, required): UID dari internship

**Response:**
```json
{
  "success": true,
  "message": "Internship details retrieved successfully",
  "data": {
    "uid": "uuid",
    "name": "Tokyo Tech Solutions",
    "description": "Perusahaan teknologi Jepang yang menawarkan program magang untuk software engineer dan IT specialist. Kesempatan bekerja dengan teknologi terkini dan tim internasional.",
    "logo_url": "https://example.com/logos/tokyo-tech.png",
    "website": "https://tokyotech.jp",
    "programs": [
      "Software Engineer Internship",
      "Data Analyst Internship",
      "UI/UX Designer Internship",
      "Project Manager Trainee"
    ],
    "benefits": [
      "Gaji kompetitif (¥200,000 - ¥300,000/bulan)",
      "Akomodasi disediakan",
      "Asuransi kesehatan",
      "Pelatihan bahasa Jepang gratis",
      "Sertifikat internasional",
      "Kesempatan kerja full-time"
    ],
    "contact_whatsapp": "+6281234567893",
    "contact_instagram": "@tokyotech_careers",
    "total_alumni": 150,
    "success_rate": 92.50,
    "display_order": 1,
    "created_at": "2026-04-29T18:00:00.000000Z",
    "updated_at": "2026-04-29T18:00:00.000000Z"
  }
}
```

### 6. Submit Internship Inquiry
**POST** `/internships/inquire`

Mengirim inquiry untuk internship.

**Request Body:**
```json
{
  "partner_uid": "uuid",
  "name": "Jane Doe",
  "email": "jane@example.com",
  "phone": "+6281234567891",
  "message": "Saya tertarik dengan Software Engineer Internship program"
}
```

**Validation Rules:**
- `partner_uid`: required, string
- `name`: required, string, max:255
- `email`: required, email, max:255
- `phone`: required, string, max:20
- `message`: nullable, string, max:1000

**Response:**
```json
{
  "success": true,
  "message": "Internship inquiry submitted successfully",
  "data": {
    "inquiry": {
      "uid": "uuid",
      "user_uid": "user-uuid",
      "type": "internship",
      "partner_uid": "partner-uuid",
      "partner_name": "Tokyo Tech Solutions",
      "name": "Jane Doe",
      "email": "jane@example.com",
      "phone": "+6281234567891",
      "message": "Saya tertarik dengan Software Engineer Internship program",
      "status": "pending",
      "submitted_at": "2026-04-29T18:00:00.000000Z",
      "created_at": "2026-04-29T18:00:00.000000Z",
      "updated_at": "2026-04-29T18:00:00.000000Z"
    }
  }
}
```

---

## User Inquiries Endpoint

### 7. Get My Inquiries
**GET** `/my-inquiries`

Mendapatkan daftar inquiry yang telah dikirim oleh user.

**Response:**
```json
{
  "success": true,
  "message": "User inquiries retrieved successfully",
  "data": [
    {
      "uid": "uuid",
      "user_uid": "user-uuid",
      "type": "jlpt_class",
      "partner_uid": "partner-uuid",
      "partner_name": "Nihongo Center Jakarta",
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+6281234567890",
      "message": "Saya tertarik dengan program N3 preparation course",
      "status": "pending",
      "submitted_at": "2026-04-29T18:00:00.000000Z",
      "created_at": "2026-04-29T18:00:00.000000Z",
      "updated_at": "2026-04-29T18:00:00.000000Z"
    },
    {
      "uid": "uuid",
      "user_uid": "user-uuid",
      "type": "internship",
      "partner_uid": "partner-uuid",
      "partner_name": "Tokyo Tech Solutions",
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+6281234567890",
      "message": "Saya tertarik dengan Software Engineer Internship program",
      "status": "contacted",
      "submitted_at": "2026-04-28T18:00:00.000000Z",
      "created_at": "2026-04-28T18:00:00.000000Z",
      "updated_at": "2026-04-29T10:00:00.000000Z"
    }
  ]
}
```

---

## Error Responses

### Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "phone": ["The phone field is required."]
  }
}
```

### Not Found Error
```json
{
  "success": false,
  "message": "JLPT class not found"
}
```

### Server Error
```json
{
  "success": false,
  "message": "Failed to get JLPT classes: Database connection error"
}
```

---

## Status Codes

- `200 OK`: Request berhasil
- `400 Bad Request`: Validation error atau bad request
- `401 Unauthorized`: Token tidak valid atau expired
- `404 Not Found`: Resource tidak ditemukan
- `500 Internal Server Error`: Server error

---

## Notes

1. Semua endpoint memerlukan authentication dengan Bearer token
2. Field `programs` dan `benefits` disimpan sebagai JSON array
3. Status inquiry: `pending`, `contacted`, `accepted`, `rejected`
4. JLPT classes dan internships hanya menampilkan yang `is_active = true` dan `is_verified = true`
5. Data diurutkan berdasarkan `display_order` dan kemudian berdasarkan nama atau success rate