<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    
}
