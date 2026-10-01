<?php

namespace App\Console\Commands;

use App\Models\RawMessage;
use App\Services\DeviceResolver;
use App\Services\SensorDataProcessorFactory;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;

class MqttListen extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Listen to CMSStaging MQTT topics and process incoming sensor data';

    public function handle(DeviceResolver $deviceResolver, SensorDataProcessorFactory $processorFactory)
    {
        $mqtt = MQTT::connection();

        $this->info('Connected to MQTT broker. Subscribing to CMSStaging/#...');

        $mqtt->subscribe('CMSStaging/#', function (string $topic, string $message) use ($deviceResolver, $processorFactory) {
            $this->handleMessage($topic, $message, $deviceResolver, $processorFactory);
        }, 0);

        $mqtt->loop(true);
    }

    protected function handleMessage(
        string $topic,
        string $message,
        DeviceResolver $deviceResolver,
        SensorDataProcessorFactory $processorFactory
    ): void {
        $payload = json_decode($message, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->warn("Invalid JSON on [{$topic}], skipping. Raw: {$message}");
            return;
        }

        $serialNumber = $payload['serial_number'] ?? null;

        $device = $deviceResolver->resolve($topic, $serialNumber);

        $raw = RawMessage::create([
            'device_id' => $device?->id,
            'topic' => $topic,
            'payload' => $payload,
            'received_at' => now(),
        ]);

        if (! $device) {
            $this->warn("Unknown device/topic [{$topic}], stored as raw only (id: {$raw->id}).");
            return;
        }

        $sensorData = $payload['sensor_data'] ?? null;
        $recordedAt = $payload['time_stamp'] ?? now()->toIso8601String();

        if (! is_array($sensorData)) {
            $this->warn("Missing sensor_data for device [{$device->serial_number}], raw stored (id: {$raw->id}).");
            return;
        }

        $processor = $processorFactory->make($device->type);

        if (! $processor) {
            $this->warn("No processor found for type [{$device->type}].");
            return;
        }

        $success = $processor->process($device, $sensorData, $recordedAt);

        if ($success) {
            $this->info("Processed [{$device->type}] data for device [{$device->serial_number}].");
        } else {
            $this->warn("Invalid sensor_data structure for device [{$device->serial_number}], type [{$device->type}]. Raw stored (id: {$raw->id}).");
        }
    }
}
