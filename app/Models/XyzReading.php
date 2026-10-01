<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XyzReading extends Model
{
    protected $fillable = [
        'device_id',
        'x',
        'y',
        'z',
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
