# Developer Quick Reference Guide

## How to Use the New Architecture

### Adding a New Mobile Feature

1. **Create Repository (if needed)**
```php
// app/Repositories/Shared/MyFeatureRepositoryInterface.php
namespace App\Repositories\Shared;

interface MyFeatureRepositoryInterface
{
    public function findByUserUid(string $userUid);
    public function create(array $data);
}

// app/Repositories/Shared/MyFeatureRepository.php
namespace App\Repositories\Shared;

use App\Models\MyFeature;

class MyFeatureRepository implements MyFeatureRepositoryInterface
{
    protected $model;

    public function __construct(MyFeature $model)
    {
        $this->model = $model;
    }

    public function findByUserUid(string $userUid)
    {
        return $this->model->where('user_uid', $userUid)->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
```

2. **Register Repository in AppServiceProvider**
```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->bind(
        \App\Repositories\Shared\MyFeatureRepositoryInterface::class,
        \App\Repositories\Shared\MyFeatureRepository::class
    );
}
```

3. **Create Mobile Service**
```php
// app/Services/Mobile/MyFeatureService.php
namespace App\Services\Mobile;

use App\Repositories\Shared\MyFeatureRepositoryInterface;

class MyFeatureService
{
    protected $featureRepository;

    public function __construct(MyFeatureRepositoryInterface $featureRepository)
    {
        $this->featureRepository = $featureRepository;
    }

    public function getUserFeatures(string $userUid): array
    {
        $features = $this->featureRepository->findByUserUid($userUid);
        
        return [
            'success' => true,
            'features' => $features,
        ];
    }
}
```

4. **Create Mobile Controller**
```php
// app/Http/Controllers/Mobile/MyFeatureController.php
namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Mobile\MyFeatureService;
use Illuminate\Http\Request;

class MyFeatureController extends Controller
{
    protected $featureService;

    public function __construct(MyFeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    public function index(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $result = $this->featureService->getUserFeatures($userUid);

            return ResponseHelper::success($result['features'], 'Features retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get features: ' . $e->getMessage());
        }
    }
}
```

5. **Add Routes**
```php
// routes/api.php
Route::middleware('auth:sanctum')->prefix('mobile')->group(function () {
    Route::get('/my-features', [Mobile\MyFeatureController::class, 'index']);
});
```

### Adding a New CMS Feature

Same as Mobile, but:
- Create service in `app/Services/CMS/`
- Create controller in `app/Http/Controllers/CMS/`
- Use namespace `App\Services\CMS`
- Add routes under `/api/cms/*`

### Common Patterns

#### Pattern 1: Simple CRUD (Mobile)
```php
// Controller
public function index(Request $request)
{
    $userUid = $request->user()->uid;
    $items = $this->repository->findByUserUid($userUid);
    return ResponseHelper::success($items);
}

public function store(Request $request)
{
    $userUid = $request->user()->uid;
    $item = $this->repository->create([
        'user_uid' => $userUid,
        ...$request->validated()
    ]);
    return ResponseHelper::success($item, 'Created successfully');
}
```

#### Pattern 2: Service with Business Logic
```php
// Service
public function processFeature(string $userUid, array $data): array
{
    // 1. Validate business rules
    if (!$this->canProcess($userUid)) {
        return ['success' => false, 'message' => 'Cannot process'];
    }

    // 2. Perform operations
    $result = $this->repository->create($data);

    // 3. Update related data
    $this->creditRepository->addPoints($userUid, 10);

    // 4. Return result
    return ['success' => true, 'result' => $result];
}
```

#### Pattern 3: CMS Admin Management
```php
// CMS Controller
public function index(Request $request)
{
    $perPage = $request->input('per_page', 15);
    $items = $this->repository->getAllPaginated($perPage);
    return ResponseHelper::success($items);
}

public function show($userUid)
{
    $items = $this->repository->findByUserUid($userUid);
    return ResponseHelper::success($items);
}
```

### Naming Conventions

#### Repositories
- Interface: `{Feature}RepositoryInterface`
- Implementation: `{Feature}Repository`
- Location: `app/Repositories/Shared/`
- Namespace: `App\Repositories\Shared`

#### Services
- Mobile: `{Feature}Service` in `app/Services/Mobile/`
- CMS: `{Feature}Service` in `app/Services/CMS/`
- Namespace: `App\Services\Mobile` or `App\Services\CMS`

#### Controllers
- Mobile: `{Feature}Controller` in `app/Http/Controllers/Mobile/`
- CMS: `{Feature}Controller` in `app/Http/Controllers/CMS/`
- Namespace: `App\Http\Controllers\Mobile` or `App\Http\Controllers\CMS`

#### Models
- Name: `{Feature}` (singular)
- Location: `app/Models/`
- Namespace: `App\Models`

### Response Helpers

Always use ResponseHelper for consistent responses:

```php
// Success response
return ResponseHelper::success($data, 'Success message');

// Error response
return ResponseHelper::error('Error message', 400);

// Not found response
return ResponseHelper::notFound('Resource not found');

// Validation error response
return ResponseHelper::validationError($validator->errors());
```

### Authentication

#### Mobile Routes
```php
Route::middleware('auth:sanctum')->prefix('mobile')->group(function () {
    // Protected routes here
});
```

#### CMS Routes
```php
Route::middleware('auth:sanctum')->prefix('cms')->group(function () {
    // Protected routes here
});
```

#### Getting Current User
```php
$user = $request->user();
$userUid = $request->user()->uid;
```

### Database Operations

#### Using Repositories
```php
// Find by user UID
$item = $this->repository->findByUserUid($userUid);

// Create
$item = $this->repository->create([
    'user_uid' => $userUid,
    'field' => 'value',
]);

// Update
$updated = $this->repository->update($uid, ['field' => 'new value']);

// Delete
$deleted = $this->repository->delete($uid);
```

#### Direct Model Usage (when appropriate)
```php
// Simple queries
$user = User::where('uid', $uid)->first();

// Relationships
$user->load('credit', 'progress');

// Transactions
DB::beginTransaction();
try {
    // operations
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

### Testing Checklist

After adding a new feature:

1. ✅ Clear caches: `php artisan config:clear && php artisan route:clear`
2. ✅ Check routes: `php artisan route:list --path=api/mobile`
3. ✅ Test with Postman/Insomnia
4. ✅ Check error handling
5. ✅ Verify authentication
6. ✅ Test validation rules
7. ✅ Check database records

### Common Commands

```bash
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# List routes
php artisan route:list
php artisan route:list --path=api/mobile
php artisan route:list --path=api/cms

# Run migrations
php artisan migrate
php artisan migrate:fresh --seed

# Generate files
php artisan make:model MyModel
php artisan make:controller Mobile/MyController
php artisan make:migration create_my_table
```

### File Structure Reference

```
app/
├── Http/Controllers/
│   ├── Mobile/
│   │   └── {Feature}Controller.php
│   └── CMS/
│       └── {Feature}Controller.php
│
├── Services/
│   ├── Mobile/
│   │   └── {Feature}Service.php
│   └── CMS/
│       └── {Feature}Service.php
│
├── Repositories/
│   └── Shared/
│       ├── {Feature}Repository.php
│       └── {Feature}RepositoryInterface.php
│
├── Models/
│   └── {Feature}.php
│
└── Providers/
    └── AppServiceProvider.php (register bindings here)
```

### Best Practices

1. **Always use repositories** for database operations
2. **Put business logic in services**, not controllers
3. **Use ResponseHelper** for all JSON responses
4. **Validate input** using Laravel's validation
5. **Use transactions** for multiple database operations
6. **Handle exceptions** properly with try-catch
7. **Use type hints** for better IDE support
8. **Follow naming conventions** consistently
9. **Keep controllers thin** - delegate to services
10. **Write clear comments** for complex logic

### Quick Troubleshooting

#### Routes not found
```bash
php artisan route:clear
php artisan config:clear
```

#### Class not found
```bash
composer dump-autoload
php artisan config:clear
```

#### Service not injecting
Check `AppServiceProvider.php` for binding

#### Authentication not working
Check middleware and token in headers:
```
Authorization: Bearer {token}
```

### Example: Complete Feature Implementation

See existing features for reference:
- **Simple CRUD**: UserNoteController
- **Business Logic**: DailyLoginService
- **Admin Management**: CMS/UserCreditController
- **Complex Flow**: CertificateService

### Need Help?

1. Check existing similar features
2. Review documentation files:
   - `ARCHITECTURE_SEPARATION_COMPLETE.md`
   - `ARCHITECTURE_DIAGRAM.md`
   - `MIGRATION_SUCCESS_SUMMARY.md`
3. Follow the patterns in existing code
4. Test thoroughly before committing

---

**Remember**: Mobile = User-facing, CMS = Admin-facing, Shared = Data access
