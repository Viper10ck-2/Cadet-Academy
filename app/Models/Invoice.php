<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Invoice extends Model {
    protected $fillable=['invoice_number','student_id','description','amount','paid_amount','due_date','status','notes'];
    protected function casts():array{return['due_date'=>'date'];}
    public function student():BelongsTo{return $this->belongsTo(User::class,'student_id');}
    public function payments():HasMany{return $this->hasMany(Payment::class);}
}
