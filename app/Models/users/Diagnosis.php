<?php

namespace App\Models\users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory;
    protected $table = 'diagnosises';
    /**
    * The attributes that are mass assignable.
    *
    * @var string[]
    */
   protected $fillable = [
       'id',
       'user_id',
       'diagnosis',
   ];
   public function users(){
    return $this->belongsToMany(User::class,'user_id' , 'id');
    }
}
