# Next Steps Implementation Guide

## ✅ What's Been Completed

1. **14 Database Tables** - All migrations run successfully
2. **14 Models** - All created with UUID support and relationships
3. **User Model** - Updated with all new relationships

## 🚀 Ready to Implement Next

### Option 1: Daily Login System (Recommended First)
**Why Start Here:** Core feature for user engagement and credits

**Files to Create:**
```
app/Repositories/DailyLoginClaimRepositoryInterface.php
app/Repositories/DailyLoginClaimRepository.php
app/Services/DailyLoginService.php
app/Http/Controllers/DailyLoginController.php
```

**Key Features:**
- Check if user can claim today
- Calculate cycle progress (7-day cycle)
- Award credits and points
- Track streak
- Reset cycle after completion

**API Endpoints:**
```
GET    /api/my-daily-login/status          - Check claim status
POST   /api/my-daily-login/claim           - Claim daily reward
GET    /api/my-daily-login/history         - Get claim history
GET    /api/admin/daily-login/{userUid}    - Admin view user claims
```

---

### Option 2: User Progress System
**Why:** Essential for tracking learning progress

**Files to Create:**
```
app/Repositories/UserProgressRepositoryInterface.php
app/Repositories/UserProgressRepository.php
app/Services/ProgressService.php
app/Http/Controllers/ProgressController.php
```

**Key Features:**
- Track scores for Hiragana, Katakana, Vocabulary
- Track JLPT level progress (N5-N1)
- Count daily lessons
- Auto-reset yesterday's lessons

**API Endpoints:**
```
GET    /api/my-progress                    - Get my progress
PUT    /api/my-progress/update             - Update progress scores
POST   /api/my-progress/lesson-complete    - Mark lesson complete
GET    /api/admin/progress/{userUid}       - Admin view user progress
```

---

### Option 3: JLPT System (Lessons & Tests)
**Why:** Core learning feature

**Files to Create:**
```
app/Repositories/JlptLessonRepositoryInterface.php
app/Repositories/JlptLessonRepository.php
app/Repositories/JlptTestScoreRepositoryInterface.php
app/Repositories/JlptTestScoreRepository.php
app/Services/JlptService.php
app/Http/Controllers/JlptController.php
```

**Key Features:**
- Track lesson completion per level
- Store pre-test and exam scores
- Calculate progress percentage
- Award credits for completion

**API Endpoints:**
```
GET    /api/jlpt/lessons/{level}           - Get lessons for level
POST   /api/jlpt/lessons/complete          - Mark lesson complete
POST   /api/jlpt/test/submit               - Submit test score
GET    /api/jlpt/scores                    - Get my test scores
GET    /api/admin/jlpt/{userUid}           - Admin view user JLPT data
```

---

### Option 4: Kanji System
**Why:** Important content feature

**Files to Create:**
```
app/Repositories/KanjiRepositoryInterface.php
app/Repositories/KanjiRepository.php
app/Services/KanjiService.php
app/Http/Controllers/KanjiController.php
database/seeders/KanjiSeeder.php
```

**Key Features:**
- Browse kanji by level
- View kanji details with examples
- Add/remove favorites
- Search kanji

**API Endpoints:**
```
GET    /api/kanji                          - List all kanji (paginated)
GET    /api/kanji/{uid}                    - Get kanji details
GET    /api/kanji/level/{level}            - Get kanji by JLPT level
POST   /api/my-kanji/favorite              - Add to favorites
DELETE /api/my-kanji/favorite/{kanjiUid}   - Remove from favorites
GET    /api/my-kanji/favorites             - Get my favorite kanji
```

---

### Option 5: Vocabulary System
**Why:** Important content feature

**Files to Create:**
```
app/Repositories/VocabularyRepositoryInterface.php
app/Repositories/VocabularyRepository.php
app/Services/VocabularyService.php
app/Http/Controllers/VocabularyController.php
database/seeders/VocabularyCategorySeeder.php
database/seeders/VocabularyWordSeeder.php
```

**Key Features:**
- Browse by category
- View word details with examples
- Add/remove favorites
- Search vocabulary

**API Endpoints:**
```
GET    /api/vocabulary/categories          - List categories
GET    /api/vocabulary/category/{uid}      - Get words by category
GET    /api/vocabulary/{uid}               - Get word details
POST   /api/my-vocabulary/favorite         - Add to favorites
DELETE /api/my-vocabulary/favorite/{wordUid} - Remove from favorites
GET    /api/my-vocabulary/favorites        - Get my favorite words
```

---

### Option 6: User Notes System
**Why:** Simple feature, good for testing

**Files to Create:**
```
app/Repositories/UserNoteRepositoryInterface.php
app/Repositories/UserNoteRepository.php
app/Services/NoteService.php
app/Http/Controllers/NoteController.php
```

**Key Features:**
- Create notes with Indonesian text
- Auto-translate to Japanese (placeholder for now)
- Edit and delete notes
- List user's notes

**API Endpoints:**
```
GET    /api/my-notes                       - List my notes
POST   /api/my-notes                       - Create note
GET    /api/my-notes/{uid}                 - Get note details
PUT    /api/my-notes/{uid}                 - Update note
DELETE /api/my-notes/{uid}                 - Delete note
```

---

### Option 7: Certificate System
**Why:** Monetization feature (costs credits)

**Files to Create:**
```
app/Repositories/CertificateRepositoryInterface.php
app/Repositories/CertificateRepository.php
app/Services/CertificateService.php
app/Http/Controllers/CertificateController.php
```

**Key Features:**
- Generate certificate (costs 60 credits)
- Check if user has passed exam
- Download certificate
- List user's certificates

**API Endpoints:**
```
POST   /api/certificates/generate          - Generate certificate (costs credits)
GET    /api/my-certificates                - List my certificates
GET    /api/my-certificates/{uid}/download - Download certificate
```

---

### Option 8: Ad Watch System
**Why:** Monetization feature (earns credits)

**Files to Create:**
```
app/Repositories/AdWatchRepositoryInterface.php
app/Repositories/AdWatchRepository.php
app/Services/AdService.php
app/Http/Controllers/AdController.php
```

**Key Features:**
- Record ad watch
- Award credits (premium: 5, regular: 2)
- Limit: 1 premium + 3 regular per day
- Track watch history

**API Endpoints:**
```
POST   /api/ads/watch                      - Record ad watch
GET    /api/my-ads/status                  - Check daily limit
GET    /api/my-ads/history                 - Get watch history
```

---

### Option 9: Leaderboard System
**Why:** Gamification feature

**Files to Create:**
```
app/Repositories/LeaderboardRepositoryInterface.php
app/Repositories/LeaderboardRepository.php
app/Services/LeaderboardService.php
app/Http/Controllers/LeaderboardController.php
```

**Key Features:**
- Calculate rankings based on total_points
- Update ranks automatically
- Get top users
- Get user's rank

**API Endpoints:**
```
GET    /api/leaderboard                    - Get top 100 users
GET    /api/leaderboard/my-rank            - Get my rank
POST   /api/admin/leaderboard/recalculate  - Recalculate all ranks
```

---

## 📝 Implementation Pattern

For each feature, follow this pattern:

### 1. Repository Interface
```php
interface FeatureRepositoryInterface
{
    public function findByUserUid(string $userUid);
    public function create(array $data);
    public function update(string $uid, array $data);
    public function delete(string $uid);
}
```

### 2. Repository Implementation
```php
class FeatureRepository implements FeatureRepositoryInterface
{
    protected $model;
    
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    
    // Implement interface methods
}
```

### 3. Service Class
```php
class FeatureService
{
    protected $repository;
    
    public function __construct(FeatureRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    // Business logic methods
}
```

### 4. Controller
```php
class FeatureController extends Controller
{
    protected $service;
    
    public function __construct(FeatureService $service)
    {
        $this->service = $service;
    }
    
    // API endpoint methods using ResponseHelper
}
```

### 5. Register in AppServiceProvider
```php
$this->app->bind(
    FeatureRepositoryInterface::class,
    FeatureRepository::class
);
```

### 6. Add Routes
```php
// User routes (authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-feature', [FeatureController::class, 'index']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/feature/{userUid}', [FeatureController::class, 'adminView']);
});
```

---

## 🎯 Recommended Implementation Order

1. **Daily Login System** - Core engagement feature
2. **User Progress System** - Essential tracking
3. **User Notes System** - Simple, good for testing
4. **JLPT System** - Core learning feature
5. **Kanji System** - Content feature (needs seeder)
6. **Vocabulary System** - Content feature (needs seeder)
7. **Ad Watch System** - Monetization
8. **Certificate System** - Monetization
9. **Leaderboard System** - Gamification

---

## 💡 Tips

- Always use `user_uid` for user relationships
- Use ResponseHelper for all JSON responses
- Implement proper validation in controllers
- Add middleware for authentication
- Test each feature before moving to next
- Create seeders for content tables (Kanji, Vocabulary)
- Document API endpoints as you create them

---

## 📚 Existing Patterns to Follow

Check these files for reference:
- `app/Repositories/UserCreditRepository.php` - Repository pattern
- `app/Services/AuthService.php` - Service pattern
- `app/Http/Controllers/UserCreditController.php` - Controller pattern
- `app/Helpers/ResponseHelper.php` - Response formatting
- `routes/api.php` - Route structure
