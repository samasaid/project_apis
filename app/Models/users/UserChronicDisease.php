<?php

namespace App\Models\users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChronicDisease extends Model
{
    use HasFactory;
    protected $table = 'user_chronic_diseases';
     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'user_id',
        'chronic_disease_id',
    ];
    public function Users(){
        return $this->belongsToMany(User::class,'user_id' , 'id');
    }
    public function chronicDiseases(){
        return $this->hasMany(ChronicDisease::class,'chronic_disease_id' , 'id');
    }
}
