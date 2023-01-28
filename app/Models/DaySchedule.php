<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaySchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'plan_id',
        'date',
        'user_id',
    ];
}
