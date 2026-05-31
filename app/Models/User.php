<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected  = [
        'username', 'real_name_encrypted', 'email', 'phone',
        'phone_verified_at', 'phone_verification_code', 'phone_code_expires_at',
        'age', 'gender', 'looking_for', 'location_city', 'location_region',
        'bio', 'locale', 'is_incognito', 'profile_complete',
        'is_suspended', 'is_banned', 'admin_role', 'age_confirmed', 'password',
    ];

    protected  = ['password', 'remember_token', 'real_name_encrypted',
        'phone_verification_code', 'phone_code_expires_at'];

    protected  = [
        'phone_verified_at' => 'datetime',
        'phone_code_expires_at' => 'datetime',
        'looking_for' => 'array',
        'is_incognito' => 'boolean',
        'profile_complete' => 'boolean',
        'is_suspended' => 'boolean',
        'is_banned' => 'boolean',
        'age_confirmed' => 'boolean',
    ];

    protected function realName(): Attribute
    {
        return Attribute::make(
            get: fn () =>  ? Crypt::decryptString() : null,
            set: fn () => ['real_name_encrypted' => Crypt::encryptString()],
        );
    }

    public function practices() { return ->belongsToMany(Practice::class, 'user_practices'); }
    public function photos() { return ->hasMany(Photo::class)->orderBy('sort_order'); }
    public function primaryPhoto() { return ->hasOne(Photo::class)->where('is_primary', true)->where('status', 'approved'); }
    public function sentMatches() { return ->hasMany(Match::class, 'requester_id'); }
    public function receivedMatches() { return ->hasMany(Match::class, 'acceptor_id'); }
    public function subscriptions() { return ->hasMany(Subscription::class); }
    public function payments() { return ->hasMany(Payment::class); }
    public function blocks() { return ->hasMany(Block::class, 'blocker_id'); }
    public function blockedBy() { return ->hasMany(Block::class, 'blocked_id'); }
    public function reports() { return ->hasMany(Report::class, 'reporter_id'); }

    public function activeSubscription()
    {
        return ->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    public function isSubscribed(): bool { return ->activeSubscription() !== null; }

    public function isAdmin(): bool { return ->admin_role !== null; }

    public function hasAdminRole(string ): bool
    {
        return in_array(->admin_role, [, 'super_admin']);
    }

    public function isBlockedBy(User ): bool
    {
        return Block::where('blocker_id', ->id)->where('blocked_id', ->id)->exists();
    }

    public function hasBlocked(User ): bool
    {
        return Block::where('blocker_id', ->id)->where('blocked_id', ->id)->exists();
    }

    public function scopeVisible()
    {
        return ->where('profile_complete', true)
            ->where('is_banned', false)
            ->where('is_suspended', false)
            ->where('is_incognito', false)
            ->whereNotNull('phone_verified_at');
    }
}
