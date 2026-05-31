<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
    protected $fillable = ['user_id','chat_id','type','amount','currency','provider','provider_payment_id','status'];
    public function user() { return $this->belongsTo(User::class); }
    public function chat() { return $this->belongsTo(Chat::class); }
}
