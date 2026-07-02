<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};

class Guardian extends Model {
    protected $table='parents';
    protected $fillable=['user_id','name','email','phone','relation','address','occupation'];
    public function user():BelongsTo{return $this->belongsTo(User::class);}
    public function students():BelongsToMany{return $this->belongsToMany(User::class,'parent_student','parent_id','student_id');}
}
