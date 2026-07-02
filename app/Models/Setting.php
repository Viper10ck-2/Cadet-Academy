<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    protected $fillable=['key','value','group','label','type','options'];
    protected function casts():array{return['options'=>'array'];}
    public static function getVal(string $key,$default=null):mixed{return static::where('key',$key)->value('value')??$default;}
}
