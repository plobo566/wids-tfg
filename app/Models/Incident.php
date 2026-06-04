<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'title',
        'entity_value',
        'status',          // 'open', 'mitigated', 'false_positive'
        'severity',        // 'low', 'medium', 'high', 'critical'
        'first_seen',
        'last_seen',
    ];

    protected $casts = [
        'first_seen' => 'datetime',
        'last_seen' => 'datetime',
    ];

    // Un incidente contiene muchas detecciones (nuestro timeline de evidencias)
    public function detections()
    {
        return $this->hasMany(Detection::class)->orderBy('created_at', 'asc');
    }
}