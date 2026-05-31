<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Practice extends Model {
    protected $fillable = ['slug','name_en','name_de','sort_order'];
    public function users() { return $this->belongsToMany(User::class, 'user_practices'); }
    public function label(string $locale = 'en'): string {
        return $locale === 'de' ? $this->name_de : $this->name_en;
    }
}
