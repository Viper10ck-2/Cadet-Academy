<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model {
    protected $fillable=['title','subtitle','image_path','link_url','link_text','is_active','order'];
    protected function casts():array{return['is_active'=>'boolean'];}
}
