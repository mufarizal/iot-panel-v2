<?php

namespace App\Http\Controllers;

use App\Services\LatestReadingService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(LatestReadingService $readings)
    {
        return view('dashboard.index', [
            'xyz' => $readings->latestXyz(),
            'current' => $readings->latestCurrent(),
            'tempHumidity' => $readings->latestTempHumidity()
        ]);
    }
}
