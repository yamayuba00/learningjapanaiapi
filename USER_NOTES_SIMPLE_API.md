# User Notes API Documentation (Simplified)

API sederhana untuk mengelola catatan user dalam bahasa Indonesia. Translation ke bahasa Jepang dilakukan di mobile app (client-side).

## Overview

- **Database**: Hanya menyimpan teks Indonesia
- **Translation**: Dilakukan di mobile app
- **API**: CRUD sederhana untuk teks Indonesia
- **Benefit**: Lebih cepat, lebih ringan, offline translation support

---

## Base URL
```
/api/mobile/notes
```

## Authentication
Semua endpoint memerlukan authentication dengan Bearer token.

---

## Endpoints

### 1. Get All Notes
**GET** `/`

Mendapatkan daftar semua notes user dengan pagination.

**Query Parameters:**
- `per_page` (optional): Jumlah data per halaman (default: 15)

**Response:**
```json
{
  "success": true,
  "message": "Notes retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "uid": "uuid",
        "user_uid": "user-uuid",
        "indonesian_text": "Saya sedang belajar hiragana",
        "created_at": "2026-04-30T16:00:00.000000Z",
        "updated_at": "2026-04-30T16:00:00.000000Z"
      },
      {
        "uid": "uuid",
        "user_uid": "user-uuid", 
        "indonesian_text": "Hari ini saya belajar kata kerja",
        "created_at": "2026-04-30T15:30:00.000000Z",
        "updated_at": "2026-04-30T15:30:00.000000Z"
      }
    ],
    "first_page_url": "http://localhost/api/mobile/notes?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/api/mobile/notes?page=1",
    "links": [...],
    "next_page_url": null,
    "path": "http://localhost/api/mobile/notes",
    "per_page": 15,
    "prev_page_url": null,
    "to": 2,
    "total": 2
  }
}
```

### 2. Create Note
**POST** `/`

Membuat note baru dengan teks Indonesia.

**Request Body:**
```json
{
  "indonesian_text": "Saya sedang belajar bahasa Jepang"
}
```

**Validation Rules:**
- `indonesian_text`: required, string, max:1000

**Response:**
```json
{
  "success": true,
  "message": "Note created successfully",
  "data": {
    "uid": "uuid",
    "user_uid": "user-uuid",
    "indonesian_text": "Saya sedang belajar bahasa Jepang",
    "created_at": "2026-04-30T16:00:00.000000Z",
    "updated_at": "2026-04-30T16:00:00.000000Z"
  }
}
```

### 3. Get Note Details
**GET** `/{uid}`

Mendapatkan detail note berdasarkan UID.

**Parameters:**
- `uid` (string, required): UID dari note

**Response:**
```json
{
  "success": true,
  "message": "Note retrieved successfully",
  "data": {
    "uid": "uuid",
    "user_uid": "user-uuid",
    "indonesian_text": "Saya sedang belajar bahasa Jepang",
    "created_at": "2026-04-30T16:00:00.000000Z",
    "updated_at": "2026-04-30T16:00:00.000000Z"
  }
}
```

### 4. Update Note
**PUT** `/{uid}`

Update note berdasarkan UID.

**Parameters:**
- `uid` (string, required): UID dari note

**Request Body:**
```json
{
  "indonesian_text": "Saya suka makanan Jepang"
}
```

**Validation Rules:**
- `indonesian_text`: sometimes, string, max:1000

**Response:**
```json
{
  "success": true,
  "message": "Note updated successfully",
  "data": {
    "uid": "uuid",
    "user_uid": "user-uuid",
    "indonesian_text": "Saya suka makanan Jepang",
    "created_at": "2026-04-30T16:00:00.000000Z",
    "updated_at": "2026-04-30T16:05:00.000000Z"
  }
}
```

### 5. Delete Note
**DELETE** `/{uid}`

Menghapus note berdasarkan UID.

**Parameters:**
- `uid` (string, required): UID dari note

**Response:**
```json
{
  "success": true,
  "message": "Note deleted successfully",
  "data": null
}
```

---

## Database Schema

Tabel `user_notes` hanya memiliki field:

```sql
CREATE TABLE user_notes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(36) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    user_uid VARCHAR(36) NOT NULL,
    indonesian_text TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user_uid) REFERENCES users(uid) ON DELETE CASCADE,
    INDEX idx_user_uid (user_uid)
);
```

---

## Error Responses

### Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "indonesian_text": ["The indonesian text field is required."]
  }
}
```

### Not Found Error
```json
{
  "success": false,
  "message": "Note not found"
}
```

### Server Error
```json
{
  "success": false,
  "message": "Failed to create note: Database connection error"
}
```

---

## Mobile App Implementation

### Translation Logic (Client-Side)

Mobile app bertanggung jawab untuk:

1. **Display Translation**: Menampilkan terjemahan Jepang dari teks Indonesia
2. **Offline Support**: Translation bisa bekerja offline dengan dictionary lokal
3. **Performance**: Tidak perlu API call untuk translation
4. **Customization**: User bisa customize translation preferences

### Suggested Mobile Implementation:

```javascript
// Example: React Native with translation library
import { translate } from 'react-native-translate';

const NoteItem = ({ note }) => {
  const [japaneseText, setJapaneseText] = useState('');
  
  useEffect(() => {
    // Translate Indonesian to Japanese
    const translateText = async () => {
      try {
        const translated = await translate(note.indonesian_text, 'id', 'ja');
        setJapaneseText(translated);
      } catch (error) {
        // Fallback to local dictionary
        setJapaneseText(localTranslate(note.indonesian_text));
      }
    };
    
    translateText();
  }, [note.indonesian_text]);
  
  return (
    <View>
      <Text style={styles.indonesian}>{note.indonesian_text}</Text>
      <Text style={styles.japanese}>{japaneseText}</Text>
    </View>
  );
};
```

---

## Benefits of This Approach

### 1. **Performance**
- Faster API responses (no translation processing)
- Reduced server load
- Better user experience

### 2. **Offline Support**
- Translation works without internet
- Local dictionary can be updated via app updates
- Better reliability

### 3. **Scalability**
- No translation API costs
- No rate limiting issues
- Easier to maintain

### 4. **Flexibility**
- Users can choose translation providers
- Custom translation preferences
- Multiple language support

### 5. **Simplicity**
- Cleaner API design
- Simpler database schema
- Easier debugging

---

## Usage Examples

### Create Note
```bash
curl -X POST /api/mobile/notes \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"indonesian_text": "Saya belajar hiragana hari ini"}'
```

### Get All Notes
```bash
curl -X GET /api/mobile/notes?per_page=10 \
  -H "Authorization: Bearer {token}"
```

### Update Note
```bash
curl -X PUT /api/mobile/notes/{uid} \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"indonesian_text": "Saya sudah selesai belajar hiragana"}'
```

### Delete Note
```bash
curl -X DELETE /api/mobile/notes/{uid} \
  -H "Authorization: Bearer {token}"
```

---

## Status Codes

- `200 OK`: Request berhasil
- `201 Created`: Note berhasil dibuat
- `400 Bad Request`: Validation error
- `401 Unauthorized`: Token tidak valid
- `404 Not Found`: Note tidak ditemukan
- `500 Internal Server Error`: Server error

---

## Best Practices

### For Mobile Developers
1. **Cache Translations**: Simpan hasil translation untuk performa
2. **Offline Dictionary**: Sediakan dictionary lokal untuk kata umum
3. **User Preferences**: Biarkan user pilih translation provider
4. **Error Handling**: Handle translation errors dengan graceful fallback

### For API Usage
1. **Pagination**: Gunakan pagination untuk list notes
2. **Validation**: Selalu validate input sebelum kirim ke API
3. **Error Handling**: Handle semua possible error responses
4. **Authentication**: Pastikan token selalu valid