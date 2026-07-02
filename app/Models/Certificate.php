<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Certificate extends Model{protected $fillable=['title','description','template_path'];public function students():BelongsToMany{return $this->belongsToMany(User::class,'certificate_student')->withPivot(['certificate_number','issue_date','expiry_date','file_path']);}}
