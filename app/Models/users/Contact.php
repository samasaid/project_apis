<?php

namespace App\Models\users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $table = 'contacts';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    public $fillable = [
        'name', 'email', 'phone', 'subject', 'message'
    ];
}
