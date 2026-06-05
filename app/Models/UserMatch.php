<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserMatch extends Model {
    protected $table = 'matches';
    protected $fillable = ['requester_id','acceptor_id','status','opening_message','opening_message_filtered','accepted_at','declined_at'];
    protected $casts = ['accepted_at'=>'datetime','declined_at'=>'datetime','opening_message_filtered'=>'boolean'];
    public function requester() { return $this->belongsTo(User::class, 'requester_id'); }
    public function acceptor() { return $this->belongsTo(User::class, 'acceptor_id'); }
    public function chat() { return $this->hasOne(Chat::class); }
}
