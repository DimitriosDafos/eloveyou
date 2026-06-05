<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;
use App\Models\UserMatch;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username', 'real_name', 'real_name_encrypted', 'email', 'phone',
        'phone_verified_at', 'phone_verification_code', 'phone_code_expires_at',
        'age', 'gender', 'looking_for', 'location_city', 'location_region',
        'bio', 'locale', 'is_incognito', 'profile_complete',
        'is_suspended', 'is_banned', 'admin_role', 'age_confirmed', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'real_name_encrypted',
        'phone_verification_code', 'phone_code_expires_at',
    ];

    protected $casts = [
        'phone_verified_at'      => 'datetime',
        'phone_code_expires_at'  => 'datetime',
        'looking_for'            => 'array',
        'is_incognito'           => 'boolean',
        'profile_complete'       => 'boolean',
        'is_suspended'           => 'boolean',
        'is_banned'              => 'boolean',
        'age_confirmed'          => 'boolean',
    ];

    protected function realName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attrs) => isset($attrs["real_name_encrypted"]) ? Crypt::decryptString($attrs["real_name_encrypted"]) : null,
            set: fn ($value) => ['real_name_encrypted' => Crypt::encryptString($value)],
        );
    }

    public function practices() { return $this->belongsToMany(Practice::class, 'user_practices'); }
    public function photos() { return $this->hasMany(Photo::class)->orderBy('sort_order'); }
    public function primaryPhoto() { return $this->hasOne(Photo::class)->where('is_primary', true)->where('status', 'approved'); }
    public function sentMatches() { return $this->hasMany(UserMatch::class, 'requester_id'); }
    public function receivedMatches() { return $this->hasMany(UserMatch::class, 'acceptor_id'); }
    public function subscriptions() { return $this->hasMany(Subscription::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function blocks() { return $this->hasMany(Block::class, 'blocker_id'); }
    public function blockedBy() { return $this->hasMany(Block::class, 'blocked_id'); }
    public function reports() { return $this->hasMany(Report::class, 'reporter_id'); }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    public function isSubscribed(): bool { return $this->activeSubscription() !== null; }
    public function isAdmin(): bool { return $this->admin_role !== null; }

    public function hasAdminRole(string $role): bool
    {
        return in_array($this->admin_role, [$role, 'super_admin']);
    }

    public function isBlockedBy(User $user): bool
    {
        return Block::where('blocker_id', $user->id)->where('blocked_id', $this->id)->exists();
    }

    public function hasBlocked(User $user): bool
    {
        return Block::where('blocker_id', $this->id)->where('blocked_id', $user->id)->exists();
    }

    public function scopeVisible($query)
    {
        return $query->where('profile_complete', true)
            ->where('is_banned', false)
            ->where('is_suspended', false)
            ->where('is_incognito', false)
            ->whereNotNull('phone_verified_at');
    }
}
