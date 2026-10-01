<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempHumidityReading extends Model
{
    protected $fillable = [
        'device_id',
        'temperature',
        'humidity',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime'
    ];
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
