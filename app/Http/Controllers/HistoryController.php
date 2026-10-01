<?php

namespace App\Http\Controllers;

use App\Exports\ReadingExport;
use App\Models\Device;
use App\Services\HistoryService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HistoryController extends Controller
{
    public function index()
    {
        $devices = Device::orderBy('serial_number')->get();

        return view('history.index', compact('devices'));
    }

    public function table(Request $request, HistoryService $history)
    {
        $validated = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $device = Device::findOrFail($validated['device_id']);

        return response()->json(
            $history->table($device->type, $device->id, $validated['from'], $validated['to'])
        );
    }

    public function chart(Request $request, HistoryService $history)
    {
        $validated = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'interval' => ['required', 'in:minute,hour,day'],
        ]);

        $device = Device::findOrFail($validated['device_id']);

        return response()->json(
            $history->chart($device->type, $device->id, $validated['from'], $validated['to'], $validated['interval'])
        );
    }

    public function export(Request $request, HistoryService $history)
    {
        $validated = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from']
        ]);

        $device = Device::findOrFail($validated['device_id']);

        $rows = $history->allForExport($device->type, $device->id, $validated['from'], $validated['to']);

        $headings = array_merge(['recorded_at'], config("cmsstaging.type_tables.{$device->type}.fields"));

        $rows = $rows->map(function ($row) use ($headings) {
            $ordered = [];
            foreach ($headings as $field) {
                $ordered[] = $row->{$field};
            }
            return $ordered;
        });

        $filename = "history-{$device->serial_number}-{$validated['from']}-to-{$validated['to']}.xlsx";

        return Excel::download(new ReadingExport($rows, $headings), $filename);
    }
}
