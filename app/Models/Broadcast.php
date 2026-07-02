<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Broadcast extends Model {
    protected $fillable=['title','message','type','recipient_ids','target_role','status','sent_at'];
    protected function casts():array{return['recipient_ids'=>'json','sent_at'=>'datetime'];}
}
