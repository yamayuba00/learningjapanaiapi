# Architecture Diagram - Separated Structure

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          CLIENT APPLICATIONS                             │
├─────────────────────────────────┬───────────────────────────────────────┤
│         Mobile App              │           CMS Admin Panel             │
│    (User-facing features)       │      (Admin management features)      │
└────────────┬────────────────────┴──────────────┬────────────────────────┘
             │                                    │
             │ API Requests                       │ API Requests
             │                                    │
┌────────────▼────────────────────────────────────▼────────────────────────┐
│                          LARAVEL API ROUTES                              │
├──────────────────────────────────┬───────────────────────────────────────┤
│   /api/mobile/*                  │   /api/cms/*                          │
│   - 60 endpoints                 │   - 19 endpoints                      │
└────────────┬─────────────────────┴──────────────┬────────────────────────┘
             │                                     │
             │                                     │
┌────────────▼─────────────────────────────────────▼────────────────────────┐
│                            CONTROLLERS LAYER                              │
├──────────────────────────────────┬────────────────────────────────────────┤
│  Mobile Controllers              │  CMS Controllers                       │
│  ├── AuthController              │  ├── AuthController                    │
│  ├── DailyLoginController        │  ├── DailyLoginController              │
│  ├── UserCreditController        │  ├── UserCreditController              │
│  ├── UserProgressController      │  └── UserProgressController            │
│  ├── JlptController              │                                        │
│  ├── CertificateController       │                                        │
│  ├── AdWatchController           │                                        │
│  ├── KanjiController             │                                        │
│  ├── VocabularyController        │                                        │
│  ├── LeaderboardController       │                                        │
│  ├── ConversationController      │                                        │
│  ├── QuizController              │                                        │
│  ├── ReferralController          │                                        │
│  └── UserNoteController          │                                        │
└────────────┬─────────────────────┴────────────────┬───────────────────────┘
             │                                       │
             │ Uses Services                         │ Uses Services
             │                                       │
┌────────────▼───────────────────────────────────────▼───────────────────────┐
│                            SERVICES LAYER                                  │
├──────────────────────────────────┬─────────────────────────────────────────┤
│  Mobile Services                 │  CMS Services                           │
│  ├── AuthService                 │  ├── AuthService                        │
│  ├── DailyLoginService           │  │   (Admin-specific methods)          │
│  ├── UserProgressService         │  └── DailyLoginService                  │
│  ├── JlptService                 │      (Admin management methods)         │
│  ├── CertificateService          │                                         │
│  └── AdWatchService              │  Note: CMS can also use Mobile services │
│                                  │  when functionality is identical        │
└────────────┬─────────────────────┴─────────────────┬───────────────────────┘
             │                                        │
             │ Uses Repositories                      │ Uses Repositories
             │                                        │
             └────────────────┬───────────────────────┘
                              │
┌─────────────────────────────▼──────────────────────────────────────────────┐
│                      SHARED REPOSITORIES LAYER                             │
│                    (Used by both Mobile & CMS)                             │
├────────────────────────────────────────────────────────────────────────────┤
│  ├── UserCreditRepository          ├── UserCreditRepositoryInterface      │
│  ├── DailyLoginClaimRepository     ├── DailyLoginClaimRepositoryInterface │
│  ├── UserProgressRepository        ├── UserProgressRepositoryInterface    │
│  ├── JlptRepository                ├── JlptRepositoryInterface            │
│  ├── UserNoteRepository            ├── UserNoteRepositoryInterface        │
│  ├── CertificateRepository         ├── CertificateRepositoryInterface     │
│  └── AdWatchRepository             └── AdWatchRepositoryInterface         │
└────────────────────────────┬───────────────────────────────────────────────┘
                             │
                             │ Database Operations
                             │
┌────────────────────────────▼───────────────────────────────────────────────┐
│                          ELOQUENT MODELS                                   │
├────────────────────────────────────────────────────────────────────────────┤
│  User, UserCredit, DailyLoginClaim, UserProgress, JlptLesson,             │
│  JlptTestScore, UserNote, Certificate, AdWatch, Kanji, KanjiExample,      │
│  KanjiFavorite, VocabularyCategory, VocabularyWord, VocabularyFavorite,   │
│  Leaderboard, Conversation, ConversationDialog, QuizHistory,              │
│  ReferralReward                                                            │
└────────────────────────────┬───────────────────────────────────────────────┘
                             │
                             │ SQL Queries
                             │
┌────────────────────────────▼───────────────────────────────────────────────┐
│                          DATABASE (SQLite)                                 │
│                         20+ Tables with UUID                               │
└────────────────────────────────────────────────────────────────────────────┘
```

## Data Flow Examples

### Mobile User Claims Daily Login Reward

```
Mobile App
    │
    │ POST /api/mobile/daily-login/claim
    ▼
Mobile\DailyLoginController
    │
    │ $dailyLoginService->claimDailyReward($userUid)
    ▼
Mobile\DailyLoginService
    │
    ├─► Shared\DailyLoginClaimRepository->create()
    │       │
    │       ▼
    │   DailyLoginClaim Model
    │       │
    │       ▼
    │   Database: daily_login_claims table
    │
    └─► Shared\UserCreditRepository->addCredits()
            │
            ▼
        UserCredit Model
            │
            ▼
        Database: user_credits table
```

### CMS Admin Adds Credits to User

```
CMS Admin Panel
    │
    │ POST /api/cms/credits/user/{userUid}/add
    ▼
CMS\UserCreditController
    │
    │ $creditRepository->addCredits($userUid, $amount)
    ▼
Shared\UserCreditRepository
    │
    │ findByUserUid() → increment('credits')
    ▼
UserCredit Model
    │
    ▼
Database: user_credits table
```

## Dependency Injection Flow

```
AppServiceProvider
    │
    │ Binds Interfaces to Implementations
    │
    ├─► UserCreditRepositoryInterface → UserCreditRepository
    ├─► DailyLoginClaimRepositoryInterface → DailyLoginClaimRepository
    ├─► UserProgressRepositoryInterface → UserProgressRepository
    ├─► JlptRepositoryInterface → JlptRepository
    ├─► UserNoteRepositoryInterface → UserNoteRepository
    ├─► CertificateRepositoryInterface → CertificateRepository
    └─► AdWatchRepositoryInterface → AdWatchRepository
        │
        │ Laravel Container Auto-resolves
        ▼
    Controllers & Services receive concrete implementations
```

## Key Design Principles

### 1. Separation of Concerns
- **Controllers**: Handle HTTP requests/responses
- **Services**: Business logic and orchestration
- **Repositories**: Data access and persistence
- **Models**: Database representation

### 2. Dependency Injection
- All dependencies injected via constructor
- Laravel container auto-resolves dependencies
- Easy to test and mock

### 3. Interface-based Programming
- Repositories implement interfaces
- Controllers depend on interfaces, not concrete classes
- Easy to swap implementations

### 4. Mobile vs CMS Separation
- **Mobile Services**: User-facing features
- **CMS Services**: Admin management features
- **Shared Repositories**: Common data access

### 5. Repository Pattern Benefits
- Abstracts database operations
- Centralizes data access logic
- Makes testing easier
- Allows switching databases without changing business logic

## Namespace Structure

```
App\
├── Http\Controllers\
│   ├── Mobile\              → Mobile app controllers
│   └── CMS\                 → CMS admin controllers
│
├── Services\
│   ├── Mobile\              → Mobile app business logic
│   └── CMS\                 → CMS admin business logic
│
├── Repositories\
│   └── Shared\              → Shared data access layer
│       ├── *Repository.php
│       └── *RepositoryInterface.php
│
├── Models\                  → Eloquent models
│
└── Helpers\
    └── ResponseHelper.php   → Consistent JSON responses
```

## Authentication Flow

```
User Login Request
    │
    ▼
AuthController (Mobile or CMS)
    │
    │ $authService->login($credentials)
    ▼
AuthService (Mobile or CMS)
    │
    ├─► User::where('email', $email)->first()
    │       │
    │       ▼
    │   User Model → Database
    │
    ├─► Hash::check($password, $user->password)
    │
    ├─► $user->createToken('auth_token')
    │       │
    │       ▼
    │   Laravel Sanctum → personal_access_tokens table
    │
    └─► Return token to controller
            │
            ▼
        JSON Response with token
```

## Summary

This architecture provides:
- ✅ Clear separation between Mobile and CMS
- ✅ Shared repositories to avoid duplication
- ✅ Easy to maintain and extend
- ✅ Testable components
- ✅ Type-safe with interfaces
- ✅ Follows SOLID principles
- ✅ Scalable for future features
