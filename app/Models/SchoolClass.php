<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};

class SchoolClass extends Model {
    protected $table='classes';
    protected $fillable=['name','code','description','instructor_id','capacity','is_active'];
    protected function casts():array{return['is_active'=>'boolean'];}
    public function instructor():BelongsTo{return $this->belongsTo(User::class,'instructor_id');}
    public function students():BelongsToMany{return $this->belongsToMany(User::class,'class_student','class_id','student_id');}
    public function schedules():HasMany{return $this->hasMany(Schedule::class,'class_id');}
    public function materials():HasMany{return $this->hasMany(Material::class,'class_id');}
    public function assignments():HasMany{return $this->hasMany(Assignment::class,'class_id');}
}
