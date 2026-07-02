<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Assignment extends Model {
    protected $fillable=['class_id','title','description','due_date','max_score','file_path'];
    protected function casts():array{return['due_date'=>'datetime'];}
    public function schoolClass():BelongsTo{return $this->belongsTo(SchoolClass::class,'class_id');}
    public function submissions():HasMany{return $this->hasMany(AssignmentSubmission::class);}
}
