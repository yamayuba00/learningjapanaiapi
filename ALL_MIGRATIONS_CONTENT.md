# All Migrations Content - Learning Japan CMS

## Instructions

Copy content dari setiap migration ke file migration yang sesuai di folder `database/migrations/`

---

## 1. daily_login_claims_table

**File:** `2026_04_29_162347_create_daily_login_claims_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_login_claims', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->integer('cycle_number');
            $table->integer('day_index');
            $table->integer('points_earned');
            $table->integer('credits_earned');
            $table->timestamp('claimed_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['user_uid', 'cycle_number', 'day_index'], 'unique_claim');
            $table->index('user_uid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_login_claims');
    }
};
```

---

## 2. user_progress_table

**File:** `2026_04_29_162356_create_user_progress_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->decimal('hiragana_score', 5, 2)->default(0.00);
            $table->decimal('katakana_score', 5, 2)->default(0.00);
            $table->decimal('vocabulary_score', 5, 2)->default(0.00);
            $table->decimal('n5_progress', 5, 2)->default(0.00);
            $table->decimal('n4_progress', 5, 2)->default(0.00);
            $table->decimal('n3_progress', 5, 2)->default(0.00);
            $table->decimal('n2_progress', 5, 2)->default(0.00);
            $table->decimal('n1_progress', 5, 2)->default(0.00);
            $table->integer('today_lessons')->default(0);
            $table->integer('yesterday_lessons')->default(0);
            $table->date('last_update_date')->nullable();
            $table->timestamps();
            
            $table->unique('user_uid');
            $table->index('user_uid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
```

---

## 3. jlpt_test_scores_table

**File:** `2026_04_29_162401_create_jlpt_test_scores_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jlpt_test_scores', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->enum('level', ['N5', 'N4', 'N3', 'N2', 'N1']);
            $table->enum('test_type', ['pretest', 'exam']);
            $table->decimal('score', 5, 2);
            $table->integer('total_questions');
            $table->integer('correct_answers');
            $table->timestamp('taken_at')->useCurrent();
            $table->timestamps();
            
            $table->index('user_uid');
            $table->index(['user_uid', 'level', 'test_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jlpt_test_scores');
    }
};
```

---

## 4. jlpt_lessons_table

**File:** `2026_04_29_162408_create_jlpt_lessons_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jlpt_lessons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->enum('level', ['N5', 'N4', 'N3', 'N2', 'N1']);
            $table->integer('lesson_index');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_uid', 'level', 'lesson_index'], 'unique_lesson');
            $table->index('user_uid');
            $table->index(['user_uid', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jlpt_lessons');
    }
};
```

---

## 5. user_notes_table

**File:** `2026_04_29_162409_create_user_notes_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->text('indonesian_text');
            $table->text('japanese_text');
            $table->timestamps();
            
            $table->index('user_uid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notes');
    }
};
```

---

## 6. certificates_table

**File:** `2026_04_29_162410_create_certificates_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->enum('level', ['N5', 'N4', 'N3', 'N2', 'N1']);
            $table->decimal('score', 5, 2);
            $table->integer('credits_spent')->default(60);
            $table->timestamp('downloaded_at')->useCurrent();
            $table->timestamps();
            
            $table->index('user_uid');
            $table->index(['user_uid', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
```

---

## 7. ad_watches_table

**File:** `2026_04_29_162412_create_ad_watches_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_watches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->enum('ad_type', ['premium', 'regular']);
            $table->integer('credits_earned');
            $table->timestamp('watched_at')->useCurrent();
            $table->date('watch_date');
            $table->timestamps();
            
            $table->index('user_uid');
            $table->index(['user_uid', 'watch_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_watches');
    }
};
```

---

## 8. leaderboard_table

**File:** `2026_04_29_162413_create_leaderboard_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboard', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->integer('total_points');
            $table->integer('rank');
            $table->timestamps();
            
            $table->unique('user_uid');
            $table->index('user_uid');
            $table->index('rank');
            $table->index('total_points');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard');
    }
};
```

---

## 9. kanji_table

**File:** `2026_04_29_162433_create_kanji_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kanji', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->string('character', 10)->unique();
            $table->text('meaning');
            $table->string('onyomi', 100)->nullable();
            $table->string('kunyomi', 100)->nullable();
            $table->enum('level', ['N5', 'N4', 'N3', 'N2', 'N1']);
            $table->integer('stroke_count');
            $table->string('stroke_order_gif', 500)->nullable();
            $table->string('radicals', 100)->nullable();
            $table->timestamps();
            
            $table->index('level');
            $table->index('character');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanji');
    }
};
```

---

## 10. kanji_examples_table

**File:** `2026_04_29_162436_create_kanji_examples_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kanji_examples', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('kanji_id')->constrained('kanji')->onDelete('cascade');
            $table->uuid('kanji_uid');
            $table->foreign('kanji_uid')->references('uid')->on('kanji')->onDelete('cascade');
            $table->string('word', 50);
            $table->string('reading', 100);
            $table->text('meaning');
            $table->timestamps();
            
            $table->index('kanji_uid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanji_examples');
    }
};
```

---

## 11. kanji_favorites_table

**File:** `2026_04_29_162438_create_kanji_favorites_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kanji_favorites', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->foreignId('kanji_id')->constrained('kanji')->onDelete('cascade');
            $table->uuid('kanji_uid');
            $table->foreign('kanji_uid')->references('uid')->on('kanji')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_uid', 'kanji_uid'], 'unique_favorite');
            $table->index('user_uid');
            $table->index('kanji_uid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanji_favorites');
    }
};
```

---

## 12. vocabulary_categories_table

**File:** `2026_04_29_162440_create_vocabulary_categories_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->string('name', 100);
            $table->string('icon', 10)->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index('display_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_categories');
    }
};
```

---

## 13. vocabulary_words_table

**File:** `2026_04_29_162440_create_vocabulary_words_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_words', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('category_id')->constrained('vocabulary_categories')->onDelete('cascade');
            $table->uuid('category_uid');
            $table->foreign('category_uid')->references('uid')->on('vocabulary_categories')->onDelete('cascade');
            $table->string('japanese', 100);
            $table->string('romaji', 100);
            $table->string('indonesian', 100);
            $table->enum('level', ['N5', 'N4', 'N3', 'N2', 'N1'])->nullable();
            $table->text('example_sentence_jp')->nullable();
            $table->text('example_sentence_romaji')->nullable();
            $table->text('example_sentence_id')->nullable();
            $table->string('audio_url', 500)->nullable();
            $table->timestamps();
            
            $table->index('category_uid');
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_words');
    }
};
```

---

## 14. vocabulary_favorites_table

**File:** `2026_04_29_162440_create_vocabulary_favorites_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_favorites', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->foreignId('word_id')->constrained('vocabulary_words')->onDelete('cascade');
            $table->uuid('word_uid');
            $table->foreign('word_uid')->references('uid')->on('vocabulary_words')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_uid', 'word_uid'], 'unique_favorite');
            $table->index('user_uid');
            $table->index('word_uid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_favorites');
    }
};
```

---

## Summary

**Total Tables:** 14 new tables
**All tables use:**
- ✅ UUID (`uid`) column for external identification
- ✅ `user_uid` foreign key for user relations (instead of `user_id`)
- ✅ Proper indexes for performance
- ✅ CASCADE delete for data integrity
- ✅ Timestamps for audit trail

**Next Steps:**
1. Copy each migration content to respective file
2. Run `php artisan migrate`
3. Create models for each table
4. Create repositories and services as needed
