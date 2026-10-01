<?php

namespace App\Services\SensorProcessors;

use App\Contracts\SensorDataProcessor;
use App\Models\Device;
use App\Models\TempHumidityReading;

class TempHumidityProcessor implements SensorDataProcessor
{
    public function process(Device $device, array $sensorData, string $recordedAt)
    {
        if (! isset($sensorData['temperature'], $sensorData['humidity'])) {
            return false;
        }

        if (! is_numeric($sensorData['temperature']) || ! is_numeric($sensorData['humidity'])) {
            return false;
        }

        TempHumidityReading::create([
            'device_id' => $device->id,
            'temperature' => $sensorData['temperature'],
            'humidity' => $sensorData['humidity'],
            'recorded_at' => $recordedAt,
        ]);

        return true;
    }
}
