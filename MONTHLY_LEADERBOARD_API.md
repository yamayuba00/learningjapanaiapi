# Monthly Leaderboard API Documentation

Sistem leaderboard bulanan yang menghitung points dari Daily Login Claims dan Ad Watches dengan reset otomatis setiap bulan.

## Overview

- **Top 10 Only**: Hanya menampilkan 10 user teratas
- **Monthly Reset**: Leaderboard reset otomatis setiap bulan
- **Points System**: +20 points per daily claim dan ad watch
- **Real-time Ranking**: Rank diupdate real-time saat user claim/watch ads

---

## Points System

### Points Sources
- **Daily Login Claim**: +20 points per claim
- **Ad Watch**: +20 points per ad watch

### Monthly Calculation
- Points dihitung dari awal bulan sampai akhir bulan
- Reset otomatis tanggal 1 setiap bulan
- Ranking berdasarkan total points tertinggi

---

## Base URL
```
/api/mobile/leaderboard
```

## Authentication
Semua endpoint memerlukan authentication dengan Bearer token.

---

## Endpoints

### 1. Get Top 10 Leaderboard
**GET** `/`

Mendapatkan top 10 leaderboard untuk bulan ini.

**Response:**
```json
{
  "success": true,
  "message": "Top leaderboard retrieved successfully",
  "data": [
    {
      "uid": "uuid",
      "user_uid": "user-uuid",
      "user_id": 1,
      "total_points": 480,
      "rank": 1,
      "month_year": "2026-04",
      "claims_count": 15,
      "ads_count": 9,
      "created_at": "2026-04-30T17:00:00.000000Z",
      "updated_at": "2026-04-30T17:30:00.000000Z",
      "user": {
        "uid": "user-uuid",
        "name": "John Doe",
        "avatar_url": "https://example.com/avatar.jpg"
      }
    },
    {
      "uid": "uuid",
      "user_uid": "user-uuid-2",
      "user_id": 2,
      "total_points": 420,
      "rank": 2,
      "month_year": "2026-04",
      "claims_count": 12,
      "ads_count": 9,
      "created_at": "2026-04-30T17:00:00.000000Z",
      "updated_at": "2026-04-30T17:25:00.000000Z",
      "user": {
        "uid": "user-uuid-2",
        "name": "Jane Smith",
        "avatar_url": "https://example.com/avatar2.jpg"
      }
    }
  ]
}
```

### 2. Get My Rank
**GET** `/my-rank`

Mendapatkan ranking dan statistik user saat ini.

**Response (User in Top 10):**
```json
{
  "success": true,
  "message": "Your rank retrieved successfully",
  "data": {
    "user_uid": "user-uuid",
    "rank": 5,
    "total_points": 280,
    "claims_count": 8,
    "ads_count": 6,
    "is_in_top": true,
    "user": {
      "uid": "user-uuid",
      "name": "Current User",
      "avatar_url": "https://example.com/my-avatar.jpg"
    }
  }
}
```

**Response (User Not in Top 10):**
```json
{
  "success": true,
  "message": "Your current stats retrieved successfully",
  "data": {
    "user_uid": "user-uuid",
    "rank": null,
    "total_points": 120,
    "claims_count": 4,
    "ads_count": 2,
    "is_in_top": false,
    "message": "You are not in the top 10 yet"
  }
}
```

---

## Database Schema

### Leaderboard Table
```sql
CREATE TABLE leaderboard (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(36) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    user_uid VARCHAR(36) NOT NULL,
    total_points INT NOT NULL DEFAULT 0,
    rank INT NOT NULL,
    month_year VARCHAR(7) NOT NULL DEFAULT '2026-04', -- Format: YYYY-MM
    claims_count INT NOT NULL DEFAULT 0,
    ads_count INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user_uid) REFERENCES users(uid) ON DELETE CASCADE,
    INDEX idx_month_rank (month_year, rank),
    INDEX idx_user_uid (user_uid)
);
```

---

## Automatic Updates

### When Points Are Added
Leaderboard otomatis diupdate saat:

1. **Daily Login Claim**: User claim daily reward
2. **Ad Watch**: User menonton iklan

### Update Process
1. Hitung total points user untuk bulan ini
2. Update/create entry di leaderboard
3. Recalculate ranking semua user
4. Update rank field untuk semua entries

---

## Monthly Reset

### Automatic Reset
- Leaderboard otomatis reset setiap awal bulan
- Triggered saat user pertama kali akses API di bulan baru
- Semua data bulan lalu dihapus, mulai fresh

### Manual Reset (Admin)
```bash
php artisan leaderboard:reset-monthly
```

---

## Points Calculation Logic

### Example Calculation
```
User Activity in April 2026:
- Daily Login Claims: 15 times = 15 × 20 = 300 points
- Ad Watches: 9 times = 9 × 20 = 180 points
- Total Points: 300 + 180 = 480 points
```

### Ranking Logic
```sql
-- Users ranked by total_points DESC
SELECT user_uid, total_points, 
       ROW_NUMBER() OVER (ORDER BY total_points DESC) as rank
FROM leaderboard 
WHERE month_year = '2026-04'
ORDER BY total_points DESC
LIMIT 10;
```

---

## Integration Points

### Daily Login Service
```php
// After successful claim
$this->leaderboardService->addClaimPoints($userUid);
```

### Ad Watch Service  
```php
// After successful ad watch
$this->leaderboardService->addAdWatchPoints($userUid);
```

---

## Error Responses

### Standard Error
```json
{
  "success": false,
  "message": "Failed to get leaderboard: Database connection error"
}
```

### User Not Found
```json
{
  "success": false,
  "message": "Failed to get rank: User not found"
}
```

---

## Performance Considerations

### Optimizations
1. **Indexing**: Index pada `month_year` dan `rank`
2. **Caching**: Cache top 10 results
3. **Batch Updates**: Batch rank recalculation
4. **Lazy Loading**: Only load user data when needed

### Database Queries
- Top 10: Single query dengan JOIN
- User Rank: Single query dengan user data
- Points Calculation: Aggregation query per user

---

## Usage Examples

### Get Top 10
```bash
curl -X GET /api/mobile/leaderboard \
  -H "Authorization: Bearer {token}"
```

### Get My Rank
```bash
curl -X GET /api/mobile/leaderboard/my-rank \
  -H "Authorization: Bearer {token}"
```

---

## Status Codes

- `200 OK`: Request berhasil
- `401 Unauthorized`: Token tidak valid
- `404 Not Found`: User tidak ditemukan
- `500 Internal Server Error`: Server error

---

## Monthly Cycle Example

### April 2026 Cycle
```
April 1: Leaderboard reset, semua user mulai dari 0 points
April 15: User A: 200 points (Rank 3), User B: 350 points (Rank 1)
April 30: Final ranking untuk April
May 1: Leaderboard reset lagi untuk May cycle
```

### Data Retention
- Data leaderboard bulan lalu dihapus saat reset
- Untuk historical data, perlu implementasi archive system
- Current implementation: hanya data bulan ini yang disimpan

---

## Future Enhancements

1. **Historical Data**: Archive leaderboard bulanan
2. **Seasonal Rewards**: Reward khusus untuk top rankers
3. **Categories**: Leaderboard berdasarkan kategori aktivitas
4. **Achievements**: Badge system untuk milestones
5. **Social Features**: Follow/compare dengan friends
6. **Push Notifications**: Notif saat naik/turun rank