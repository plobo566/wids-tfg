<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    protected $table = 'security_events';

    protected $fillable = [
        'ip_address',
        'method',
        'url',
        'user_agent',
        'status_code',
        'payload',
        
    ];


    public function detections(){
        return $this->hasMany(Detection::class);
    }
}
