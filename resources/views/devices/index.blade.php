@extends('layouts.app')
@section('title', 'Devices — CMSStaging')
@section('page-label', 'Devices')
@section('content')
<div id="devices-page" data-endpoint="{{ route('devices.latest-data') }}" data-timezone="{{ config('app.timezone') }}" data-timezone-offset="{{ now()->format('P') }}">
    <div class="page-heading"><div><p class="eyebrow">PEMANTAUAN LANGSUNG</p><h2>Monitor perangkat</h2><p>Ikuti pembacaan terbaru perangkat tanpa memuat ulang halaman.</p></div><span class="quiet-badge"><span class="status-dot"></span>Pembaruan setiap 3 detik</span></div>
    <div class="panel filter-toolbar"><div class="field device-select-field"><label for="device-selector">Tampilkan device</label><select id="device-selector"><option value="all">Semua Device</option>@foreach($current->concat($tempHumidity)->concat($xyz) as $item)<option value="{{ $item->device_id }}">{{ $item->name ?? 'Device '.$item->serial_number }}</option>@endforeach</select></div><p>Pilih satu device untuk melihat pembacaan lebih dekat.</p><span class="quiet-badge" data-visible-count>{{ $current->count() + $tempHumidity->count() + $xyz->count() }} device</span></div>
    <p class="inline-notice" role="status" data-poll-status hidden></p>
    <p class="inline-notice" data-chart-error hidden>Grafik belum dapat dimuat. Nilai perangkat tetap diperbarui secara otomatis.</p>
    @include('partials.sensor-groups', ['live' => true])
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
@endpush
