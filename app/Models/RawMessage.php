<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMessage extends Model
{
    protected $fillable = [
        'device_id',
        'topic',
        'payload',
        'received_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
