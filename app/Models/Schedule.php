<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model {
    protected $fillable=['class_id','day','start_time','end_time','room'];
    public function schoolClass():BelongsTo{return $this->belongsTo(SchoolClass::class,'class_id');}
}

