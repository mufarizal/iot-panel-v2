<?php

namespace App\Services;

use App\Contracts\SensorDataProcessor;
use App\Services\SensorProcessors\CurrentProcessor;
use App\Services\SensorProcessors\TempHumidityProcessor;
use App\Services\SensorProcessors\XyzProcessor;

class SensorDataProcessorFactory
{
    protected array $map = [
        'xyz' => XyzProcessor::class,
        'current' => CurrentProcessor::class,
        'temp_humidity' => TempHumidityProcessor::class,
    ];

    public function make(string $type): ?SensorDataProcessor
    {
        $class = $this->map[$type] ?? null;

        if (! $class) {
            return null;
        }

        return app($class);
    }
}
