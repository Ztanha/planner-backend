<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoSchedule extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'plan_id',
        'weekdays',
        'user_id',
    ];
    function user(){
        return $this->belongsTo(User::class);
    }
}
