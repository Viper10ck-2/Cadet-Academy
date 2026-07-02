<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model {
    protected $fillable=['name','type','content','status','scheduled_at','sent_at','recipients_count'];
    protected function casts():array{return['scheduled_at'=>'datetime','sent_at'=>'datetime'];}
}
