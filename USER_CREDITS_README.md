# User Credits System - Documentation

## Overview
Sistem User Credits dengan Repository Pattern untuk mengelola kredit, poin, streak, dan cycle pengguna. Sistem ini menggunakan UUID (uid) untuk identifikasi eksternal dan ID untuk internal database.

## Database Schema

### Table: `user_credits`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Primary key (internal) |
| uid | UUID (UNIQUE) | Universal unique identifier (external) |
| user_id | INT (FK) | Foreign key ke tabel users |
| credits | INT | Kredit saat ini (default: 0) |
| total_points | INT | Total poin yang pernah dikumpulkan (default: 0) |
| streak | INT | Jumlah hari berturut-turut (default: 0) |
| cycle_number | INT | Nomor siklus (default: 1) |
| cycle_start_date | DATE | Tanggal mulai siklus |
| last_claim_date | DATE | Tanggal klaim terakhir |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diupdate |

## Architecture

### Repository Pattern
```
Controller → Repository Interface → Repository Implementation → Model
```

### Files Structure
```
app/
├── Helpers/
│   └── ResponseHelper.php (NEW)
├── Http/Controllers/
│   └── UserCreditController.php
├── Models/
│   ├── User.php (updated)
│   └── UserCredit.php
├── Repositories/
│   ├── UserCreditRepositoryInterface.php
│   └── UserCreditRepository.php
└── Providers/
    └── AppServiceProvider.php (updated)

database/migrations/
├── 2026_04_29_144250_create_user_credits_table.php
└── 2026_04_29_144758_add_uid_to_user_credits_table.php

routes/
└── api.php
```

## Response Helper

### ResponseHelper Methods

```php
// Success responses
ResponseHelper::success($data, $message, $statusCode = 200)
ResponseHelper::created($data, $message)

// Error responses
ResponseHelper::error($message, $errors, $statusCode = 400)
ResponseHelper::validationError($errors, $message)
ResponseHelper::notFound($message)
ResponseHelper::unauthorized($message)
ResponseHelper::forbidden($message)
ResponseHelper::serverError($message, $errors)

// Other
ResponseHelper::noContent()
```

### Response Format

**Success Response:**
```json
{
  "success": true,
  "message": "Success message",
  "data": {...}
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": {...}
}
```

## API Endpoints

### Base URL: `/api/user-credits`

#### 1. Get All User Credits
```http
GET /api/user-credits
```

**Response:**
```json
{
  "success": true,
  "message": "User credits retrieved successfully",
  "data": [...]
}
```

#### 2. Create User Credit
```http
POST /api/user-credits
```

**Request Body:**
```json
{
  "user_id": 1,
  "credits": 0,
  "total_points": 0,
  "streak": 0,
  "cycle_number": 1,
  "cycle_start_date": "2026-04-29",
  "last_claim_date": null
}
```

**Response:**
```json
{
  "success": true,
  "message": "User credit created successfully",
  "data": {
    "id": 1,
    "uid": "9d501d4e-442a-da54-82b6-0f5e237e0f36",
    "user_id": 1,
    "credits": 0,
    ...
  }
}
```

#### 3. Get User Credit by ID (Internal)
```http
GET /api/user-credits/id/{id}
```

#### 4. Get User Credit by UID (External)
```http
GET /api/user-credits/uid/{uid}
```

**Example:**
```http
GET /api/user-credits/uid/9d501d4e-442a-da54-82b6-0f5e237e0f36
```

#### 5. Get User Credit by User ID
```http
GET /api/user-credits/user/{userId}
```

#### 6. Update User Credit by ID
```http
PUT /api/user-credits/id/{id}
```

#### 7. Update User Credit by UID
```http
PUT /api/user-credits/uid/{uid}
```

**Request Body:**
```json
{
  "credits": 100,
  "streak": 5
}
```

#### 8. Delete User Credit by ID
```http
DELETE /api/user-credits/id/{id}
```

#### 9. Delete User Credit by UID
```http
DELETE /api/user-credits/uid/{uid}
```

#### 10. Add Credits by User ID
```http
POST /api/user-credits/user/{userId}/add-credits
```

**Request Body:**
```json
{
  "amount": 50
}
```

#### 11. Add Credits by UID
```http
POST /api/user-credits/uid/{uid}/add-credits
```

#### 12. Deduct Credits by User ID
```http
POST /api/user-credits/user/{userId}/deduct-credits
```

**Request Body:**
```json
{
  "amount": 30
}
```

#### 13. Deduct Credits by UID
```http
POST /api/user-credits/uid/{uid}/deduct-credits
```

#### 14. Update Streak by User ID
```http
POST /api/user-credits/user/{userId}/update-streak
```

#### 15. Update Streak by UID
```http
POST /api/user-credits/uid/{uid}/update-streak
```

**Logic:**
- Jika belum pernah claim: streak = 1
- Jika claim kemarin: streak + 1
- Jika sudah claim hari ini: tidak berubah
- Jika terputus: streak = 1

#### 16. Reset Cycle by User ID
```http
POST /api/user-credits/user/{userId}/reset-cycle
```

#### 17. Reset Cycle by UID
```http
POST /api/user-credits/uid/{uid}/reset-cycle
```

**Action:**
- Increment cycle_number
- Set cycle_start_date ke hari ini
- Reset credits ke 0
- Reset streak ke 0

## Repository Methods

### UserCreditRepositoryInterface

```php
// Basic CRUD
public function all(): Collection;
public function find(int $id): ?UserCredit;
public function findByUid(string $uid): ?UserCredit;
public function findByUserId(int $userId): ?UserCredit;
public function create(array $data): UserCredit;
public function update(int $id, array $data): bool;
public function updateByUid(string $uid, array $data): bool;
public function delete(int $id): bool;
public function deleteByUid(string $uid): bool;

// Credit Operations
public function addCredits(int $userId, int $amount): bool;
public function addCreditsByUid(string $uid, int $amount): bool;
public function deductCredits(int $userId, int $amount): bool;
public function deductCreditsByUid(string $uid, int $amount): bool;

// Streak & Cycle
public function updateStreak(int $userId): bool;
public function updateStreakByUid(string $uid): bool;
public function resetCycle(int $userId): bool;
public function resetCycleByUid(string $uid): bool;
```

## Usage Examples

### 1. Menggunakan ResponseHelper

```php
use App\Helpers\ResponseHelper;

// Success response
return ResponseHelper::success($data, 'Operation successful');

// Created response
return ResponseHelper::created($newResource, 'Resource created');

// Validation error
return ResponseHelper::validationError($validator->errors());

// Not found
return ResponseHelper::notFound('User not found');

// Custom error
return ResponseHelper::error('Something went wrong', null, 500);
```

### 2. Menggunakan Repository di Service/Controller Lain

```php
use App\Repositories\UserCreditRepositoryInterface;
use App\Helpers\ResponseHelper;

class SomeService
{
    protected UserCreditRepositoryInterface $creditRepo;

    public function __construct(UserCreditRepositoryInterface $creditRepo)
    {
        $this->creditRepo = $creditRepo;
    }

    public function rewardUser(string $uid, int $points)
    {
        $success = $this->creditRepo->addCreditsByUid($uid, $points);
        
        if (!$success) {
            return ResponseHelper::error('Failed to add credits');
        }
        
        return ResponseHelper::success(null, 'Credits added successfully');
    }
}
```

### 3. Eloquent Relationships

```php
// Get user with credits
$user = User::with('credit')->find(1);
$credits = $user->credit->credits;
$uid = $user->credit->uid;

// Get user from credit
$userCredit = UserCredit::with('user')->find(1);
$userName = $userCredit->user->name;
```

### 4. Testing dengan Postman/Insomnia

**Create User Credit:**
```bash
POST http://localhost/api/user-credits
Content-Type: application/json

{
  "user_id": 1
}
```

**Add Credits by UID:**
```bash
POST http://localhost/api/user-credits/uid/9d501d4e-442a-da54-82b6-0f5e237e0f36/add-credits
Content-Type: application/json

{
  "amount": 100
}
```

**Get by UID:**
```bash
GET http://localhost/api/user-credits/uid/9d501d4e-442a-da54-82b6-0f5e237e0f36
```

## Migration

Jalankan migration:
```bash
php artisan migrate
```

Rollback migration:
```bash
php artisan migrate:rollback
```

Regenerate autoload (setelah menambah helper):
```bash
composer dump-autoload
```

## Key Features

### 1. UUID Support
- **UID**: Auto-generated UUID untuk identifikasi eksternal (API)
- **ID**: Integer primary key untuk internal database operations
- Model menggunakan `getRouteKeyName()` untuk route model binding dengan UID

### 2. ResponseHelper
- Konsisten JSON response format
- Predefined status codes
- Easy to use static methods
- Centralized response handling

### 3. Dual Identifier Support
- Semua operasi tersedia untuk ID dan UID
- Flexibility untuk internal dan external usage
- Better security dengan UUID exposure

## Notes

1. **Foreign Key Constraint**: `user_id` terhubung ke tabel `users` dengan `ON DELETE CASCADE`
2. **Unique Constraints**: 
   - Setiap user hanya bisa memiliki 1 record di `user_credits`
   - UID adalah unique identifier
3. **Default Values**: Credits, points, streak dimulai dari 0, cycle_number dari 1
4. **Date Handling**: Menggunakan Carbon untuk manipulasi tanggal
5. **Validation**: Semua input divalidasi di controller level
6. **Auto UUID**: UUID di-generate otomatis saat create record baru
7. **Response Format**: Semua response menggunakan ResponseHelper untuk konsistensi

## Best Practices

### Kapan Menggunakan ID vs UID?

**Gunakan ID untuk:**
- Internal operations
- Database joins
- Performance-critical queries
- Admin/internal tools

**Gunakan UID untuk:**
- Public API endpoints
- External integrations
- Client-facing operations
- Security (tidak expose database structure)

## Future Enhancements

- [ ] Add transaction history table
- [ ] Implement credit expiration
- [ ] Add reward tiers based on total_points
- [ ] Create scheduled task untuk auto-reset cycle
- [ ] Add events & listeners untuk credit changes
- [ ] Implement caching untuk frequently accessed data
- [ ] Add API rate limiting
- [ ] Implement audit logging
