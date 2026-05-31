<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Chat extends Model {
    protected $fillable = ['match_id','requester_id','acceptor_id','expires_at','photos_revealed'];
    protected $casts = ['expires_at'=>'datetime','photos_revealed'=>'boolean'];
    public function match() { return $this->belongsTo(Match::class); }
    public function requester() { return $this->belongsTo(User::class, 'requester_id'); }
    public function acceptor() { return $this->belongsTo(User::class, 'acceptor_id'); }
    public function messages() { return $this->hasMany(Message::class)->orderBy('created_at'); }
    public function isExpired(): bool { return $this->expires_at && $this->expires_at->isPast(); }
    public function otherUser(User $user): User {
        return $user->id === $this->requester_id ? $this->acceptor : $this->requester;
    }
}
