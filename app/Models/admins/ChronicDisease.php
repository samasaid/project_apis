<?php

namespace App\Models\admins;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChronicDisease extends Model
{
    use HasFactory;
    protected $table = 'chronic_diseases';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'chronic_disease',
        'description',
        'treatment',
        'syndrome',
    ];
}
