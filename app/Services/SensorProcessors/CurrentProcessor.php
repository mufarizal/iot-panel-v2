<?php

namespace App\Services\SensorProcessors;

use App\Contracts\SensorDataProcessor;
use App\Models\CurrentReading;
use App\Models\Device;

class CurrentProcessor implements SensorDataProcessor
{
    public function process(Device $device, array $sensorData, string $recordedAt)
    {
        if (! isset($sensorData['current']) || ! is_numeric($sensorData['current'])) {
            return false;
        }

        CurrentReading::create([
            'device_id' => $device->id,
            'current' => $sensorData['current'],
            'recored_at' => $recordedAt,
        ]);

        return true;
    }
}
