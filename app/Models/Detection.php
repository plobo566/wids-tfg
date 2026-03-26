<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detection extends Model
{

    protected $fillable = [

        'security_event_id',
        'type',
        'rule_name',
        'entity_type',
        'entity_value',
        'score',
        'window_start',
        'window_end',
        'details',

    ];


    protected $casts =[
        'details' => 'array',
        'window_start' => 'datetime',
        'window_end' => 'datetime',
    ];


    public function securityEvent(){
        return $this->belongsTo(SecurityEvent::class);
    }
}
