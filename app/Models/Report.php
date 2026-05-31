<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Report extends Model {
    protected $fillable = ['reporter_id','reported_user_id','photo_id','message_id','type','description','status','reviewed_by','admin_notes','reviewed_at'];
    protected $casts = ['reviewed_at'=>'datetime'];
    public function reporter() { return $this->belongsTo(User::class, 'reporter_id'); }
    public function reportedUser() { return $this->belongsTo(User::class, 'reported_user_id'); }
    public function photo() { return $this->belongsTo(Photo::class); }
    public function message() { return $this->belongsTo(Message::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
