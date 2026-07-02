<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model {
    protected $fillable=['payment_number','invoice_id','amount','method','reference','proof_path','status','notes','verified_by','verified_at'];
    protected function casts():array{return['verified_at'=>'datetime'];}
    public function invoice():BelongsTo{return $this->belongsTo(Invoice::class);}
    public function verifier():BelongsTo{return $this->belongsTo(User::class,'verified_by');}
}
