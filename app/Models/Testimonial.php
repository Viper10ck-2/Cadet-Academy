<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model {
    protected $fillable=['name','role','content','avatar_path','rating','is_published','order'];
    protected function casts():array{return['is_published'=>'boolean'];}
}
