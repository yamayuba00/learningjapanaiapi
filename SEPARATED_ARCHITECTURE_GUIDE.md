# Separated Architecture Guide
## Mobile vs CMS - Repositories & Services

**Date**: April 30, 2026  
**Purpose**: Memisahkan Repositories dan Services untuk kemudahan maintenance

---

## 📂 New Structure

```
app/
├── Repositories/
│   ├── Mobile/                    # Mobile App Repositories (Read-focused)
│   │   ├── UserCreditRepositoryInterface.php
│   │   ├── UserCreditRepository.php
│   │   ├── DailyLoginRepositoryInterface.php
│   │   ├── DailyLoginRepository.php
│   │   ├── ProgressRepositoryInterface.php
│   │   ├── ProgressRepository.php
│   │   ├── JlptRepositoryInterface.php
│   │   ├── JlptRepository.php
│   │   ├── NoteRepositoryInterface.php
│   │   ├── NoteRepository.php
│   │   ├── CertificateRepositoryInterface.php
│   │   ├── CertificateRepository.php
│   │   ├── AdWatchRepositoryInterface.php
│   │   ├── AdWatchRepository.php
│   │   ├── QuizRepositoryInterface.php
│   │   ├── QuizRepository.php
│   │   └── ReferralRepositoryInterface.php
│   │   └── ReferralRepository.php
│   │
│   └── CMS/                       # CMS Admin Repositories (Full CRUD)
│       ├── UserCreditRepositoryInterface.php
│       ├── UserCreditRepository.php
│       ├── DailyLoginRepositoryInterface.php
│       ├── DailyLoginRepository.php
│       ├── ProgressRepositoryInterface.php
│       ├── ProgressRepository.php
│       └── UserManagementRepositoryInterface.php
│       └── UserManagementRepository.php
│
├── Services/
│   ├── Mobile/                    # Mobile App Services
│   │   ├── AuthService.php
│   │   ├── DailyLoginService.php
│   │   ├── ProgressService.php
│   │   ├── JlptService.php
│   │   ├── NoteService.php
│   │   ├── CertificateService.php
│   │   ├── AdWatchService.php
│   │   ├── QuizService.php
│   │   └── ReferralService.php
│   │
│   └── CMS/                       # CMS Admin Services
│       ├── AdminAuthService.php
│       ├── UserManagementService.php
│       ├── CreditManagementService.php
│       ├── DailyLoginManagementService.php
│       └── ProgressManagementService.php
│
└── Http/Controllers/
    ├── Mobile/                    # Already separated ✅
    └── CMS/                       # Already separated ✅
```

---

## 🎯 Key Differences

### Mobile Repositories
**Focus**: Read operations + limited writes (user's own data)

**Characteristics**:
- Mostly read operations
- User can only access their own data
- Limited write operations (notes, favorites)
- No admin operations

**Example**:
```php
namespace App\Repositories\Mobile;

interface UserCreditRepositoryInterface
{
    public function findByUserUid(string $userUid);
    public function getCycleInfo(string $userUid);
    public function getUserRank(string $userUid);
    // No add/deduct methods - read only!
}
```

### CMS Repositories
**Focus**: Full CRUD operations for all users

**Characteristics**:
- Full CRUD operations
- Access to all users' data
- Admin operations (add/deduct credits, block users, etc.)
- Bulk operations
- Statistics and reporting

**Example**:
```php
namespace App\Repositories\CMS;

interface UserCreditRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15);
    public function findByUserUid(string $userUid);
    public function create(array $data);
    public function update(string $uid, array $data);
    public function addCredits(string $userUid, int $amount);
    public function deductCredits(string $userUid, int $amount);
    public function delete(string $uid): bool;
    // Full CRUD + admin operations
}
```

---

## 📝 Implementation Examples

### 1. Mobile User Credit Repository

```php
<?php

namespace App\Repositories\Mobile;

use App\Models\UserCredit;

class UserCreditRepository implements UserCreditRepositoryInterface
{
    protected $model;

    public function __construct(UserCredit $model)
    {
        $this->model = $model;
    }

    /**
     * Get user's own credit (read-only)
     */
    public function findByUserUid(string $userUid)
    {
        return $this->model->where('user_uid', $userUid)->first();
    }

    /**
     * Get cycle information
     */
    public function getCycleInfo(string $userUid)
    {
        $credit = $this->findByUserUid($userUid);

        if (!$credit) {
            return null;
        }

        return [
            'cycle_number' => $credit->cycle_number,
            'cycle_start_date' => $credit->cycle_start_date,
            'last_claim_date' => $credit->last_claim_date,
            'streak' => $credit->streak,
        ];
    }

    /**
     * Get user's rank
     */
    public function getUserRank(string $userUid)
    {
        $credit = $this->findByUserUid($userUid);

        if (!$credit) {
            return null;
        }

        $rank = $this->model->where('total_points', '>', $credit->total_points)->count() + 1;

        return [
            'rank' => $rank,
            'total_points' => $credit->total_points,
        ];
    }
}
```

### 2. CMS User Credit Repository

```php
<?php

namespace App\Repositories\CMS;

use App\Models\UserCredit;
use Carbon\Carbon;

class UserCreditRepository implements UserCreditRepositoryInterface
{
    protected $model;

    public function __construct(UserCredit $model)
    {
        $this->model = $model;
    }

    /**
     * Get all credits with pagination
     */
    public function getAllPaginated(int $perPage = 15)
    {
        return $this->model->with('user')->paginate($perPage);
    }

    /**
     * Find by user UID
     */
    public function findByUserUid(string $userUid)
    {
        return $this->model->where('user_uid', $userUid)->first();
    }

    /**
     * Create new credit
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update credit
     */
    public function update(string $uid, array $data)
    {
        $credit = $this->model->where('uid', $uid)->first();
        if ($credit) {
            $credit->update($data);
            return $credit;
        }
        return null;
    }

    /**
     * Add credits (admin operation)
     */
    public function addCredits(string $userUid, int $amount)
    {
        $credit = $this->findByUserUid($userUid);
        if ($credit) {
            $credit->increment('credits', $amount);
            return true;
        }
        return false;
    }

    /**
     * Deduct credits (admin operation)
     */
    public function deductCredits(string $userUid, int $amount)
    {
        $credit = $this->findByUserUid($userUid);
        if ($credit && $credit->credits >= $amount) {
            $credit->decrement('credits', $amount);
            return true;
        }
        return false;
    }

    /**
     * Add points (admin operation)
     */
    public function addPoints(string $userUid, int $amount)
    {
        $credit = $this->findByUserUid($userUid);
        if ($credit) {
            $credit->increment('total_points', $amount);
            return true;
        }
        return false;
    }

    /**
     * Update streak (admin operation)
     */
    public function updateStreak(string $userUid, int $streak)
    {
        $credit = $this->findByUserUid($userUid);
        if ($credit) {
            $credit->update(['streak' => $streak]);
            return true;
        }
        return false;
    }

    /**
     * Reset cycle (admin operation)
     */
    public function resetCycle(string $userUid)
    {
        $credit = $this->findByUserUid($userUid);
        if ($credit) {
            $credit->update([
                'cycle_number' => $credit->cycle_number + 1,
                'cycle_start_date' => Carbon::today(),
                'last_claim_date' => null,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Get top users by points
     */
    public function getTopUsersByPoints(int $limit = 10)
    {
        return $this->model->with('user')
            ->orderBy('total_points', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Delete credit (admin operation)
     */
    public function delete(string $uid): bool
    {
        $credit = $this->model->where('uid', $uid)->first();
        return $credit ? $credit->delete() : false;
    }
}
```

---

## 🔧 Service Layer Separation

### Mobile Services
**Focus**: Business logic for user operations

```php
<?php

namespace App\Services\Mobile;

use App\Repositories\Mobile\UserCreditRepositoryInterface;

class CreditService
{
    protected $creditRepository;

    public function __construct(UserCreditRepositoryInterface $creditRepository)
    {
        $this->creditRepository = $creditRepository;
    }

    /**
     * Get user's credit information
     */
    public function getUserCredit(string $userUid)
    {
        return $this->creditRepository->findByUserUid($userUid);
    }

    /**
     * Get user's balance
     */
    public function getBalance(string $userUid)
    {
        $credit = $this->creditRepository->findByUserUid($userUid);
        
        return [
            'credits' => $credit->credits ?? 0,
            'total_points' => $credit->total_points ?? 0,
        ];
    }

    /**
     * Get user's rank
     */
    public function getRank(string $userUid)
    {
        return $this->creditRepository->getUserRank($userUid);
    }
}
```

### CMS Services
**Focus**: Admin operations and management

```php
<?php

namespace App\Services\CMS;

use App\Repositories\CMS\UserCreditRepositoryInterface;

class CreditManagementService
{
    protected $creditRepository;

    public function __construct(UserCreditRepositoryInterface $creditRepository)
    {
        $this->creditRepository = $creditRepository;
    }

    /**
     * Get all users' credits
     */
    public function getAllCredits(int $perPage = 15)
    {
        return $this->creditRepository->getAllPaginated($perPage);
    }

    /**
     * Add credits to user (admin operation)
     */
    public function addCreditsToUser(string $userUid, int $amount, string $reason = null)
    {
        $success = $this->creditRepository->addCredits($userUid, $amount);

        // Log the operation
        if ($success) {
            // TODO: Log admin action
        }

        return [
            'success' => $success,
            'amount' => $amount,
            'reason' => $reason,
        ];
    }

    /**
     * Deduct credits from user (admin operation)
     */
    public function deductCreditsFromUser(string $userUid, int $amount, string $reason = null)
    {
        $success = $this->creditRepository->deductCredits($userUid, $amount);

        // Log the operation
        if ($success) {
            // TODO: Log admin action
        }

        return [
            'success' => $success,
            'amount' => $amount,
            'reason' => $reason,
        ];
    }

    /**
     * Get credit statistics
     */
    public function getStatistics()
    {
        // TODO: Implement statistics
        return [
            'total_users' => 0,
            'total_credits_distributed' => 0,
            'total_points_earned' => 0,
        ];
    }
}
```

---

## 📋 AppServiceProvider Bindings

Update `app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Mobile Repositories
        $this->app->bind(
            \App\Repositories\Mobile\UserCreditRepositoryInterface::class,
            \App\Repositories\Mobile\UserCreditRepository::class
        );

        $this->app->bind(
            \App\Repositories\Mobile\DailyLoginRepositoryInterface::class,
            \App\Repositories\Mobile\DailyLoginRepository::class
        );

        // ... other mobile repositories

        // CMS Repositories
        $this->app->bind(
            \App\Repositories\CMS\UserCreditRepositoryInterface::class,
            \App\Repositories\CMS\UserCreditRepository::class
        );

        $this->app->bind(
            \App\Repositories\CMS\DailyLoginRepositoryInterface::class,
            \App\Repositories\CMS\DailyLoginRepository::class
        );

        // ... other CMS repositories
    }
}
```

---

## 🎯 Benefits of Separation

### 1. **Clear Responsibility**
- Mobile: User operations only
- CMS: Admin operations only

### 2. **Easier Maintenance**
- Changes to mobile don't affect CMS
- Changes to CMS don't affect mobile
- Clear separation of concerns

### 3. **Better Security**
- Mobile repositories can't perform admin operations
- Reduced risk of accidental privilege escalation

### 4. **Improved Testability**
- Test mobile and CMS separately
- Mock dependencies easily
- Clear test boundaries

### 5. **Scalability**
- Can optimize mobile repositories for read performance
- Can optimize CMS repositories for admin operations
- Independent scaling strategies

---

## 🔄 Migration Strategy

### Step 1: Create New Structure
```bash
# Already done ✅
mkdir app/Repositories/Mobile
mkdir app/Repositories/CMS
mkdir app/Services/Mobile
mkdir app/Services/CMS
```

### Step 2: Move Existing Files
```bash
# Move and update namespaces
# Mobile repositories - focus on read operations
# CMS repositories - full CRUD operations
```

### Step 3: Update Controllers
```php
// Mobile Controller
use App\Services\Mobile\CreditService;

class UserCreditController extends Controller
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }
}

// CMS Controller
use App\Services\CMS\CreditManagementService;

class UserCreditController extends Controller
{
    protected $creditManagementService;

    public function __construct(CreditManagementService $creditManagementService)
    {
        $this->creditManagementService = $creditManagementService;
    }
}
```

### Step 4: Update Service Provider
```php
// Register both Mobile and CMS bindings
```

### Step 5: Test
```bash
php artisan test
```

---

## 📝 Naming Conventions

### Mobile
- **Repositories**: `{Feature}Repository`
- **Services**: `{Feature}Service`
- **Focus**: User operations

### CMS
- **Repositories**: `{Feature}Repository` (same name, different namespace)
- **Services**: `{Feature}ManagementService`
- **Focus**: Admin operations

---

## ✅ Checklist

### Repositories to Separate
- [ ] UserCreditRepository (Mobile + CMS)
- [ ] DailyLoginClaimRepository (Mobile + CMS)
- [ ] UserProgressRepository (Mobile + CMS)
- [ ] JlptRepository (Mobile only)
- [ ] UserNoteRepository (Mobile only)
- [ ] CertificateRepository (Mobile only)
- [ ] AdWatchRepository (Mobile only)
- [ ] QuizRepository (Mobile only)
- [ ] ReferralRepository (Mobile only)

### Services to Separate
- [ ] AuthService (Mobile + CMS)
- [ ] DailyLoginService (Mobile + CMS)
- [ ] ProgressService (Mobile + CMS)
- [ ] JlptService (Mobile only)
- [ ] NoteService (Mobile only)
- [ ] CertificateService (Mobile only)
- [ ] AdWatchService (Mobile only)
- [ ] QuizService (Mobile only)
- [ ] ReferralService (Mobile only)

---

## 🎉 Conclusion

Dengan pemisahan ini:
- ✅ **Maintenance lebih mudah** - Jelas mana Mobile, mana CMS
- ✅ **Security lebih baik** - Mobile tidak bisa akses operasi admin
- ✅ **Code lebih clean** - Separation of concerns
- ✅ **Testing lebih mudah** - Test Mobile dan CMS terpisah
- ✅ **Scalability lebih baik** - Bisa optimize masing-masing

---

**Status**: ✅ Structure Created  
**Next**: Implement all repositories and services with new structure
