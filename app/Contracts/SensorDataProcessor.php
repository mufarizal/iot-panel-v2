<?php

namespace App\Contracts;

use App\Models\Device;

interface SensorDataProcessor
{
    public function process(Device $device, array $sensorData, string $recordedAt);
}
