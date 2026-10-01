<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LatestReadingService
{
    public function latestXyz()
    {
        return DB::table('xyz_readings')->select(DB::raw('DISTINCT ON (xyz_readings.device_id) xyz_readings.*, devices.serial_number, devices.name'))
            ->join('devices', 'devices.id', '=', 'xyz_readings.device_id')->orderBy('xyz_readings.device_id')->orderBy('xyz_readings.recorded_at', 'desc')->get();
    }

    public function latestCurrent()
    {
        return DB::table('current_readings')->select(DB::raw('DISTINCT ON (current_readings.device_id) current_readings.*, devices.serial_number, devices.name'))
            ->join('devices', 'devices.id', '=', 'current_readings.device_id')->orderBy('current_readings.device_id')->orderBy('current_readings.recorded_at', 'desc')->get();
    }
    public function latestTempHumidity()
    {
        return DB::table('temp_humidity_readings')->select(DB::raw('DISTINCT ON (temp_humidity_readings.device_id) temp_humidity_readings.*, devices.serial_number, devices.name'))
            ->join('devices', 'devices.id', '=', 'temp_humidity_readings.device_id')->orderBy('temp_humidity_readings.device_id')->orderBy('temp_humidity_readings.recorded_at', 'desc')->get();
    }
}
