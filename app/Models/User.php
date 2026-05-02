<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

#[Hidden(['password', 'remember_token', 'email_verification_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_verification_sent_at' => 'datetime',
            'email_verification_otp_expires_at' => 'datetime',
            'password_reset_otp_expires_at' => 'datetime',
            'last_login' => 'datetime',
            'blocked_at' => 'datetime',
            'is_blocked' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot function to auto-generate UUID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uid)) {
                $model->uid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uid';
    }

    /**
     * Get the user's credit.
     * Using uid instead of id
     */
    public function credit(): HasOne
    {
        return $this->hasOne(UserCredit::class, 'user_uid', 'uid');
    }

    /**
     * Get the user's daily login claims.
     */
    public function dailyLoginClaims()
    {
        return $this->hasMany(DailyLoginClaim::class, 'user_uid', 'uid');
    }

    /**
     * Get the user's progress.
     */
    public function progress()
    {
        return $this->hasOne(UserProgress::class, 'user_uid', 'uid');
    }

    /**
     * Get the user's JLPT lessons.
     */
    public function jlptLessons()
    {
        return $this->hasMany(JlptLesson::class, 'user_uid', 'uid');
    }

    /**
     * Get the user's JLPT test scores.
     */
    public function jlptTestScores()
    {
        return $this->hasMany(JlptTestScore::class, 'user_uid', 'uid');
    }

    /**
     * Get the user's notes.
     */
    public function notes()
    {
        return $this->hasMany(UserNote::class, 'user_uid', 'uid');
    }

    /**
     * Get the user's certificates.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'user_uid', 'uid');
    }

    /**
     * Get the user's ad watches.
     */
    public function adWatches()
    {
        return $this->hasMany(AdWatch::class, 'user_uid', 'uid');
    }

    /**
     * Get the user's leaderboard entry.
     */
    public function leaderboard()
    {
        return $this->hasOne(Leaderboard::class, 'user_uid', 'uid');
    }

    /**
     * Get the user's favorite kanji.
     */
    public function favoriteKanji()
    {
        return $this->belongsToMany(Kanji::class, 'kanji_favorites', 'user_uid', 'kanji_uid', 'uid', 'uid')
            ->withTimestamps();
    }

    /**
     * Get the user's favorite vocabulary words.
     */
    public function favoriteWords()
    {
        return $this->belongsToMany(VocabularyWord::class, 'vocabulary_favorites', 'user_uid', 'word_uid', 'uid', 'uid')
            ->withTimestamps();
    }

    /**
     * Get the user's quiz history.
     */
    public function quizHistory()
    {
        return $this->hasMany(QuizHistory::class, 'user_uid', 'uid');
    }

    /**
     * Get referrals made by this user.
     */
    public function referralsMade()
    {
        return $this->hasMany(ReferralReward::class, 'referrer_user_uid', 'uid');
    }

    /**
     * Get referral reward received by this user.
     */
    public function referralReceived()
    {
        return $this->hasOne(ReferralReward::class, 'referred_user_uid', 'uid');
    }

    /**
     * Check if user is blocked
     */
    public function isBlocked(): bool
    {
        return $this->is_blocked;
    }

    /**
     * Check if email is verified
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Get user's partnership inquiries
     */
    public function partnershipInquiries()
    {
        return $this->hasMany(PartnershipInquiry::class, 'user_uid', 'uid');
    }
}