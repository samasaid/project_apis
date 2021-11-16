<?php

namespace App\Models\users;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'full_name',
        'national_id',
        'mobile',
        'address',
        'date_of_birth',
        'blood_type',
        'sex',
        'social_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    public function userChronicDiseases(){
        return $this->hasMany(UserChronicDisease::class,'user_id' , 'id');
    }
    public function diagnosises(){
        return $this->hasMany(Diagnosis::class,'user_id' , 'id');
    }
     // this function for add assets folder before image filepath
   public function getPhotoAttribute($val){
    return ($val !== null) ? asset('assets/'.$val): "";
   }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
