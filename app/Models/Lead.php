<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model {
    protected $fillable=['name','email','phone','source','status','notes','assigned_to','contacted_at'];
    protected function casts():array{return['contacted_at'=>'datetime'];}
    public function assignedTo():BelongsTo{return $this->belongsTo(User::class,'assigned_to');}
}
