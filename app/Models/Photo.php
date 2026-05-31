<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Photo extends Model {
    protected $fillable = ['user_id','path','is_primary','sort_order','status','removal_reason'];
    protected $casts = ['is_primary' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function getUrlAttribute(): string {
        return asset('storage/'.$this->path);
    }
}
