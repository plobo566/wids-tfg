<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable =[
        'name',
        'class_name',
        'is_enabled',
        'priority',
        'settings'
    ];

    protected $casts =[
        'settings' => 'array', //para castear de json en bbdd a array
        'is_enabled' => 'boolean',
    ];
}
