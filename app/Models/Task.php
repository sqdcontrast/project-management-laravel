<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'status',
        'project_id',
        'assigned_to',
    ];

    protected $attributes = [
        'status' => 'to_do',
    ];
}
