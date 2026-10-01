<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class HistoryService
{
    protected array $allowedIntervals = ['minute', 'hour', 'day'];

    public function table(string $type, int $deviceId, string $from, string $to, int $perPage = 25)
    {
        $config = config("cmsstaging.type_tables.{$type}");

        return DB::table($config['table'])
            ->where('device_id', $deviceId)
            ->whereBetween('recorded_at', [$from, $to])
            ->orderBy('recorded_at', 'desc')
            ->paginate($perPage);
    }

    public function chart(string $type, int $deviceId, string $from, string $to, string $interval)
    {
        if (! in_array($interval, $this->allowedIntervals)) {
            $interval = 'minute';
        }

        $config = config("cmsstaging.type_tables.{$type}");

        $aggregates = collect($config['fields'])
            ->map(fn($field) => "AVG({$field}) as {$field}")
            ->implode(', ');

        return DB::table($config['table'])
            ->select(DB::raw("date_trunc('{$interval}', recorded_at) as bucket, {$aggregates}"))
            ->where('device_id', $deviceId)
            ->whereBetween('recorded_at', [$from, $to])
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();
    }

    public function allForExport(string $type, int $deviceId, string $from, string $to)
    {
        $config = config("cmsstaging.type_tables.{$type}");

        return DB::table($config['table'])
            ->where('device_id', $deviceId)->whereBetween('recorded_at', [$from, $to])->orderBy('recorded_at')
            ->get();
    }
}
