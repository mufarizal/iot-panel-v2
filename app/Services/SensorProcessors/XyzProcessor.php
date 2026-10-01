<?php

namespace App\Services\SensorProcessors;

use App\Contracts\SensorDataProcessor;
use App\Models\Device;
use App\Models\XyzReading;

class XyzProcessor implements SensorDataProcessor
{
    public function process(Device $device, array $sensorData, string $recordedAt)
    {
        if (! isset($sensorData['x'], $sensorData['y'], $sensorData['z'])) {
            return false;
        }

        if (! is_numeric($sensorData['x']) || ! is_numeric($sensorData['y']) || ! is_numeric($sensorData['z'])) {
            return false;
        }

        XyzReading::create([
            'device_id' => $device->id,
            'x' => $sensorData['x'],
            'y' => $sensorData['y'],
            'z' => $sensorData['z'],
            'recorded_at' => $recordedAt,
        ]);

        return true;
    }
}
