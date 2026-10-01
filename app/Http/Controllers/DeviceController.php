<?php

namespace App\Http\Controllers;

use App\Services\LatestReadingService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DeviceController extends Controller
{
    public function index(LatestReadingService $readings)
    {
        return view('devices.index', [
            'xyz' => $readings->latestXyz(),
            'current' => $readings->latestCurrent(),
            'tempHumidity' => $readings->latestTempHumidity()
        ]);
    }

    public function latestData(LatestReadingService $readings)
    {
        return response()->json([
            'current' => $this->formatCollection($readings->latestCurrent()),
            'temp_humidity' => $this->formatCollection($readings->latestTempHumidity()),
            'xyz' => $this->formatCollection($readings->latestXyz())
        ]);
    }

    protected function formatCollection($collection)
    {
        return $collection->map(function ($item) {
            $item->recorded_at_human = Carbon::parse($item->recorded_at)->diffForHumans();
            return $item;
        });
    }
}
