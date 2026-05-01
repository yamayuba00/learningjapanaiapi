# Quick Start Guide - Learning Japan CMS

## 🚀 Getting Started

### Prerequisites
- PHP 8.1+
- Composer
- MySQL/SQLite
- Laravel 11

### Installation
```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Start server
php artisan serve
```

---

## 📱 Testing Mobile App API

### 1. Register User
```bash
curl -X POST http://localhost:8000/api/mobile/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### 2. Login
```bash
curl -X POST http://localhost:8000/api/mobile/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Save the token from response!**

### 3. Get Profile
```bash
curl -X GET http://localhost:8000/api/mobile/auth/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 4. Get Credits
```bash
curl -X GET http://localhost:8000/api/mobile/credits \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 5. Check Daily Login Status
```bash
curl -X GET http://localhost:8000/api/mobile/daily-login/status \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 6. Claim Daily Reward
```bash
curl -X POST http://localhost:8000/api/mobile/daily-login/claim \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 🖥️ Testing CMS API

### 1. Admin Login
```bash
curl -X POST http://localhost:8000/api/cms/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "adminpassword"
  }'
```

### 2. Get All Users
```bash
curl -X GET http://localhost:8000/api/cms/users \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### 3. Get User Credit
```bash
curl -X GET http://localhost:8000/api/cms/credits/user/USER_UID \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### 4. Add Credits to User
```bash
curl -X POST http://localhost:8000/api/cms/credits/user/USER_UID/add \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 50,
    "reason": "Bonus reward"
  }'
```

### 5. Block User
```bash
curl -X POST http://localhost:8000/api/cms/users/USER_UID/block \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "reason": "Violation of terms"
  }'
```

---

## 📊 Available Routes

### Mobile App (17 routes)
```
✅ POST   /api/mobile/auth/register
✅ POST   /api/mobile/auth/login
✅ POST   /api/mobile/auth/logout
✅ GET    /api/mobile/auth/profile
✅ PUT    /api/mobile/auth/profile
✅ POST   /api/mobile/auth/change-password
✅ GET    /api/mobile/auth/verify-email/{token}
✅ POST   /api/mobile/auth/resend-verification
✅ POST   /api/mobile/auth/refresh-token

✅ GET    /api/mobile/credits
✅ GET    /api/mobile/credits/balance
✅ GET    /api/mobile/credits/streak
✅ GET    /api/mobile/credits/cycle

✅ GET    /api/mobile/daily-login/status
✅ POST   /api/mobile/daily-login/claim
✅ GET    /api/mobile/daily-login/history
✅ GET    /api/mobile/daily-login/can-claim
```

### CMS Admin (19 routes)
```
✅ POST   /api/cms/auth/login
✅ POST   /api/cms/auth/logout
✅ GET    /api/cms/auth/profile

✅ GET    /api/cms/users
✅ GET    /api/cms/users/{userUid}
✅ POST   /api/cms/users/{userUid}/block
✅ POST   /api/cms/users/{userUid}/unblock

✅ GET    /api/cms/credits
✅ GET    /api/cms/credits/top-users
✅ GET    /api/cms/credits/statistics
✅ GET    /api/cms/credits/user/{userUid}
✅ POST   /api/cms/credits/user/{userUid}/add
✅ POST   /api/cms/credits/user/{userUid}/deduct
✅ POST   /api/cms/credits/user/{userUid}/add-points
✅ POST   /api/cms/credits/user/{userUid}/update-streak
✅ POST   /api/cms/credits/user/{userUid}/reset-cycle

✅ GET    /api/cms/daily-login/user/{userUid}/status
✅ GET    /api/cms/daily-login/user/{userUid}/history
✅ POST   /api/cms/daily-login/user/{userUid}/manual-claim
```

---

## 🗂️ Project Structure

```
app/
├── Http/Controllers/
│   ├── Mobile/              # Mobile App Controllers
│   │   ├── AuthController.php
│   │   ├── UserCreditController.php
│   │   └── DailyLoginController.php
│   ├── CMS/                 # CMS Admin Controllers
│   │   ├── AuthController.php
│   │   ├── UserCreditController.php
│   │   └── DailyLoginController.php
│   └── (Legacy controllers)
│
├── Services/                # Business Logic
│   ├── AuthService.php
│   └── DailyLoginService.php
│
├── Repositories/            # Data Access Layer
│   ├── UserCreditRepository.php
│   ├── UserCreditRepositoryInterface.php
│   ├── DailyLoginClaimRepository.php
│   └── DailyLoginClaimRepositoryInterface.php
│
├── Models/                  # Eloquent Models
│   ├── User.php
│   ├── UserCredit.php
│   ├── DailyLoginClaim.php
│   └── (14 other models)
│
└── Helpers/
    └── ResponseHelper.php   # Consistent JSON responses
```

---

## 💾 Database

### Tables (20 total)
```
Core Tables:
- users
- user_credits
- personal_access_tokens

Learning System:
- daily_login_claims
- user_progress
- jlpt_lessons
- jlpt_test_scores
- user_notes
- certificates
- ad_watches
- leaderboard
- kanji
- kanji_examples
- kanji_favorites
- vocabulary_categories
- vocabulary_words
- vocabulary_favorites
```

### Check Migrations
```bash
php artisan migrate:status
```

### Rollback (if needed)
```bash
php artisan migrate:rollback
```

### Fresh Migration
```bash
php artisan migrate:fresh
```

---

## 🔧 Useful Commands

### List All Routes
```bash
php artisan route:list
```

### List Mobile Routes Only
```bash
php artisan route:list --path=mobile
```

### List CMS Routes Only
```bash
php artisan route:list --path=cms
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Run Tinker (Test Models)
```bash
php artisan tinker

# Test commands:
>>> User::count()
>>> UserCredit::count()
>>> DailyLoginClaim::count()
>>> User::first()
```

---

## 📝 Daily Login Rewards

| Day | Credits | Points | Notes |
|-----|---------|--------|-------|
| 1   | 5       | 10     | Start |
| 2   | 5       | 10     |       |
| 3   | 10      | 20     |       |
| 4   | 10      | 20     |       |
| 5   | 15      | 30     |       |
| 6   | 15      | 30     |       |
| 7   | 25      | 50     | Bonus! |

**Total per cycle**: 85 credits, 170 points

---

## 🐛 Troubleshooting

### Issue: Token Invalid
**Solution**: Make sure to include `Bearer` prefix
```bash
Authorization: Bearer YOUR_TOKEN
```

### Issue: 404 Not Found
**Solution**: Clear route cache
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: Database Connection Error
**Solution**: Check `.env` file
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Issue: Class Not Found
**Solution**: Regenerate autoload
```bash
composer dump-autoload
```

---

## 📚 Documentation Files

1. **API_STRUCTURE_DOCUMENTATION.md** - Complete API reference
2. **IMPLEMENTATION_PHASE_3_SUMMARY.md** - Implementation details
3. **QUICK_START_GUIDE.md** - This file
4. **NEXT_STEPS_GUIDE.md** - Future implementation guide
5. **CURRENT_SYSTEM_STATE.md** - System overview

---

## 🎯 Next Steps

### Immediate:
1. Test all mobile endpoints
2. Test all CMS endpoints
3. Add admin middleware
4. Create Postman collection

### Future Features:
1. User Progress System
2. JLPT Lessons & Tests
3. Kanji Management
4. Vocabulary Management
5. Certificate System
6. Ad Watch System
7. Leaderboard System

---

## 💡 Tips

1. **Always use Bearer token** in Authorization header
2. **Check response format** - all responses follow same structure
3. **Use ResponseHelper** for consistent responses
4. **Follow the pattern** when adding new features
5. **Test with Postman** for easier debugging

---

## 🔗 Useful Links

- Laravel Documentation: https://laravel.com/docs
- Sanctum Documentation: https://laravel.com/docs/sanctum
- Postman: https://www.postman.com/

---

**Last Updated**: April 29, 2026  
**Version**: 3.0  
**Status**: Production Ready
