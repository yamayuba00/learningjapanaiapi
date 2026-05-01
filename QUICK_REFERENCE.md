# Quick Reference - User Credits System

## 🚀 Quick Commands

```bash
# Setup
php artisan migrate
composer dump-autoload
php artisan db:seed --class=UserCreditSeeder

# Start Server
php artisan serve

# Test
curl http://localhost:8000/api/user-credits
```

## 📍 Test UIDs

```
User 1: 1de98089-3228-4870-a650-ddf09bef75b1
User 2: 212405c5-6afe-4335-b847-d201a85cf5c4
```

## 🔗 Common Endpoints

```bash
# Get All
GET /api/user-credits

# Get by UID
GET /api/user-credits/uid/{uid}

# Add Credits
POST /api/user-credits/uid/{uid}/add-credits
Body: {"amount": 100}

# Deduct Credits
POST /api/user-credits/uid/{uid}/deduct-credits
Body: {"amount": 50}

# Update Streak
POST /api/user-credits/uid/{uid}/update-streak

# Reset Cycle
POST /api/user-credits/uid/{uid}/reset-cycle
```

## 💻 Code Snippets

### Using Repository
```php
use App\Repositories\UserCreditRepositoryInterface;

public function __construct(
    private UserCreditRepositoryInterface $repo
) {}

// Find by UID
$credit = $this->repo->findByUid($uid);

// Add credits
$this->repo->addCreditsByUid($uid, 100);
```

### Using ResponseHelper
```php
use App\Helpers\ResponseHelper;

// Success
return ResponseHelper::success($data);

// Error
return ResponseHelper::error('Error message');

// Not Found
return ResponseHelper::notFound();

// Validation Error
return ResponseHelper::validationError($errors);
```

### Model Relationships
```php
// User -> Credit
$user = User::with('credit')->find(1);
$credits = $user->credit->credits;

// Credit -> User
$credit = UserCredit::with('user')->first();
$userName = $credit->user->name;
```

## 📁 File Locations

```
app/
├── Helpers/ResponseHelper.php
├── Repositories/
│   ├── UserCreditRepositoryInterface.php
│   └── UserCreditRepository.php
├── Models/UserCredit.php
└── Http/Controllers/UserCreditController.php

routes/api.php
database/seeders/UserCreditSeeder.php
```

## 🎯 Response Format

```json
{
  "success": true,
  "message": "Success message",
  "data": {...}
}
```

## 🔧 Repository Methods

```php
// CRUD
all()
find($id)
findByUid($uid)
findByUserId($userId)
create($data)
update($id, $data)
updateByUid($uid, $data)
delete($id)
deleteByUid($uid)

// Operations
addCredits($userId, $amount)
addCreditsByUid($uid, $amount)
deductCredits($userId, $amount)
deductCreditsByUid($uid, $amount)
updateStreak($userId)
updateStreakByUid($uid)
resetCycle($userId)
resetCycleByUid($uid)
```

## 📊 Database Schema

```sql
user_credits
├── id (PK)
├── uid (UUID, UNIQUE)
├── user_id (FK -> users.id)
├── credits (INT, default: 0)
├── total_points (INT, default: 0)
├── streak (INT, default: 0)
├── cycle_number (INT, default: 1)
├── cycle_start_date (DATE)
├── last_claim_date (DATE)
├── created_at
└── updated_at
```

## 🎨 ResponseHelper Methods

```php
success($data, $message, $code = 200)
created($data, $message)
error($message, $errors, $code = 400)
validationError($errors, $message)
notFound($message)
unauthorized($message)
forbidden($message)
serverError($message, $errors)
noContent()
```

## 📖 Documentation Files

- `USER_CREDITS_README.md` - Full documentation
- `API_TEST_GUIDE.md` - Testing guide
- `CHANGELOG_USER_CREDITS.md` - Version history
- `IMPLEMENTATION_SUMMARY.md` - Implementation details
- `QUICK_REFERENCE.md` - This file

## 🔍 Troubleshooting

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Regenerate autoload
composer dump-autoload

# Check routes
php artisan route:list --path=user-credits

# Check database
php artisan db:show
php artisan db:table user_credits
```

## ⚡ Quick Tests

```bash
# Test 1: Get all
curl http://localhost:8000/api/user-credits

# Test 2: Get by UID
curl http://localhost:8000/api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1

# Test 3: Add credits
curl -X POST http://localhost:8000/api/user-credits/uid/1de98089-3228-4870-a650-ddf09bef75b1/add-credits \
  -H "Content-Type: application/json" \
  -d '{"amount": 100}'
```

## 🎯 Key Features

- ✅ Repository Pattern
- ✅ UUID Support
- ✅ Response Helper
- ✅ Dual Identifiers (ID & UID)
- ✅ Credit Management
- ✅ Streak Tracking
- ✅ Cycle Management
- ✅ Validation
- ✅ Error Handling
- ✅ Documentation

---

**For detailed information, see USER_CREDITS_README.md**
