<?php

namespace App\Services;

use App\Models\Device;

class DeviceResolver
{
    public function resolve(string $topic, ?string $serialNumber)
    {
        if (empty($serialNumber)) {
            return null;
        }

        $device = Device::where('serial_number', $serialNumber)->first();

        if ($device) {
            return $device;
        }

        $type = config("cmsstaging.topic_type_map.{$serialNumber}");

        if (!$type) {
            return null;
        }

        return Device::create([
            'serial_number' => $serialNumber,
            'topic' => $topic,
            'type' => $type,
            'is_active' => true,
        ]);
    }
}
