<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model {
    protected $fillable=['class_id','title','description','content','file_path','order'];
    public function schoolClass():BelongsTo{return $this->belongsTo(SchoolClass::class,'class_id');}
}
