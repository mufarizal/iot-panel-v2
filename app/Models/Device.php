<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'serial_number',
        'topic',
        'type',
        'name',
        'is_active',
    ];

    public function rawMessages()
    {
        return $this->hasMany(RawMessage::class);
    }
    public function xyzReadings()
    {
        return $this->hasMany(XyzReading::class);
    }

    public function currentReadings()
    {
        return $this->hasMany(CurrentReading::class);
    }

    public function tempHumidityReadings()
    {
        return $this->hasMany(TempHumidityReading::class);
    }
}
