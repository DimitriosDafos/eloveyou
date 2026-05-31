<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Subscription extends Model {
    protected $fillable = ['user_id','plan','chat_limit','chats_used','status','provider','provider_subscription_id','started_at','expires_at'];
    protected $casts = ['started_at'=>'datetime','expires_at'=>'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function isActive(): bool { return $this->status === 'active' && $this->expires_at->isFuture(); }
    public function hasChatsLeft(): bool {
        if ($this->chat_limit === null) return true;
        return $this->chats_used < $this->chat_limit;
    }
}
