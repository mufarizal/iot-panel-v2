@extends('layouts.app')
@php($isExport = request('view') === 'export')
@section('title', ($isExport ? 'Export' : 'History').' — CMSStaging')
@section('page-label', $isExport ? 'Export' : 'History')
@section('content')
<div id="history-page" data-table-endpoint="{{ route('history.table') }}" data-chart-endpoint="{{ route('history.chart') }}" data-export-endpoint="{{ route('history.export') }}" data-export-only="{{ $isExport ? 'true' : 'false' }}" data-timezone="{{ config('app.timezone') }}" data-timezone-offset="{{ now()->format('P') }}">
    <div class="page-heading"><div><p class="eyebrow">{{ $isExport ? 'UNDUH LAPORAN' : 'TELUSURI PEMBACAAN' }}</p><h2>{{ $isExport ? 'Ekspor data perangkat' : 'Riwayat perangkat' }}</h2><p>{{ $isExport ? 'Simpan pembacaan perangkat dalam file Excel untuk analisis lebih lanjut.' : 'Temukan data sebelumnya dan lihat perubahan dari waktu ke waktu.' }}</p></div><span class="page-heading-icon"><x-icon :name="$isExport ? 'export' : 'history'" class="h-7 w-7" /></span></div>
    <section class="panel history-filter" aria-labelledby="filter-heading">
        <div class="filter-heading"><div><h3 id="filter-heading">{{ $isExport ? 'Siapkan unduhan Anda' : 'Filter riwayat' }}</h3><p>Pilih device dan rentang tanggal yang ingin ditampilkan.</p></div><span class="step-badge">{{ $isExport ? 'EXCEL · .XLSX' : 'SESUAI KEBUTUHAN ANDA' }}</span></div>
        <form id="history-filter" class="history-filter-form">
            <div class="field"><label for="history-device">Device</label><select id="history-device" name="device_id" required @disabled($devices->isEmpty())>@forelse($devices as $device)<option value="{{ $device->id }}" data-type="{{ $device->type }}" @selected((string)request('device_id') === (string)$device->id)>{{ $device->name ?? 'Device '.$device->serial_number }}</option>@empty<option value="">Belum ada device</option>@endforelse</select></div>
            <div class="field"><label for="history-from">Dari tanggal</label><input id="history-from" type="date" name="from" value="{{ now()->toDateString() }}" required></div>
            <div class="field"><label for="history-to">Sampai tanggal</label><input id="history-to" type="date" name="to" value="{{ now()->toDateString() }}" required></div>
            <div class="field" id="interval-field" hidden><label for="history-interval">Rata-rata</label><select id="history-interval" name="interval"><option value="minute">Per Menit</option><option value="hour" selected>Per Jam</option><option value="day">Per Hari</option></select></div>
            @unless($isExport)<button type="submit" class="button-primary" @disabled($devices->isEmpty())>Terapkan Filter <x-icon name="arrow" class="h-4 w-4" /></button>@endunless
        </form>
        <div class="filter-bottom"><p><x-icon name="calendar" class="h-4 w-4" />Tanggal akhir mencakup seluruh hari · {{ config('app.timezone') }}</p><a id="download-excel" class="{{ $isExport ? 'button-primary' : 'button-secondary' }}" aria-disabled="true"><x-icon name="export" class="h-4 w-4" />Unduh Excel</a></div>
        <p id="filter-message" class="inline-notice" role="status" hidden></p>
    </section>
    @if($devices->isEmpty())<div class="panel empty-state"><x-icon name="devices" class="h-8 w-8" /><h3>Belum ada device</h3><p>Riwayat dan unduhan tersedia setelah perangkat terdaftar.</p></div>@endif
    @if($isExport)
        <div class="export-explainer"><span class="section-icon"><x-icon name="export" /></span><div><h3>Siap untuk diolah lebih lanjut</h3><p>File Excel berisi seluruh pembacaan pada rentang yang dipilih. Untuk rentang yang panjang, proses unduh dapat memerlukan waktu lebih lama.</p><a class="text-link" href="{{ route('history') }}">Lihat riwayat sebelum mengunduh <x-icon name="arrow" class="h-4 w-4" /></a></div></div>
    @else
        <section class="panel history-results" aria-labelledby="results-heading">
            <div class="results-toolbar"><div><h3 id="results-heading">Hasil pembacaan</h3><p id="result-summary">Pilih filter, lalu klik Terapkan Filter.</p></div><div class="view-switch" role="group" aria-label="Tampilan riwayat"><button type="button" data-history-mode="table" aria-pressed="true">Tabel</button><button type="button" data-history-mode="chart" aria-pressed="false">Grafik</button></div></div>
            <div id="history-status" class="empty-state" role="status"><x-icon name="history" class="h-8 w-8" /><h4>Mulai dari rentang waktu</h4><p>Data akan tampil setelah Anda menerapkan filter.</p></div>
            <div id="table-view" hidden><div class="table-scroll"><table><caption class="sr-only">Pembacaan perangkat pada rentang yang dipilih</caption><thead id="history-table-head"></thead><tbody id="history-table-body"></tbody></table></div><div class="pagination"><p id="pagination-summary"></p><div class="flex gap-2"><button type="button" id="previous-page" class="button-secondary">Sebelumnya</button><button type="button" id="next-page" class="button-secondary">Berikutnya</button></div></div></div>
            <div id="chart-view" hidden><div id="history-chart-legend" class="chart-legend"></div><div id="history-chart" role="img" aria-label="Grafik rata-rata pembacaan perangkat"></div><p id="chart-note" class="chart-note"></p></div>
        </section>
    @endif
    <noscript><p class="inline-notice">Aktifkan JavaScript untuk memuat riwayat dan menyiapkan unduhan.</p></noscript>
</div>
@endsection
@push('scripts')
@if(!$isExport)<script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>@endif
@endpush
