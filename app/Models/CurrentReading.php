<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrentReading extends Model
{
    protected $fillable = [
        'device_id',
        'current',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
