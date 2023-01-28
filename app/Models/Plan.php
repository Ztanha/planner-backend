<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'task_id',
        'start',
        'end',
        'user_id',
        'plan_id',
    ];

}
