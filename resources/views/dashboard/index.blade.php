@extends('layouts.app')
@section('title', 'Dashboard — CMSStaging')
@section('page-label', 'Dashboard')
@section('content')
    <div class="page-heading"><div><p class="eyebrow">GAMBARAN UMUM</p><h2>Ringkasan monitoring</h2><p>Kondisi terakhir perangkat Anda, dalam satu tampilan.</p></div><a class="button-primary" href="{{ route('devices') }}">Pantau perangkat <x-icon name="arrow" class="h-4 w-4" /></a></div>
    <div class="summary-grid">
        @foreach([['current', 'Device arus', $current->count()], ['temp_humidity', 'Device suhu & kelembapan', $tempHumidity->count()], ['xyz', 'Device X / Y / Z', $xyz->count()]] as [$icon, $label, $count])
            <div class="panel summary-card"><div><p>{{ $label }}</p><strong>{{ $count }}<span>device</span></strong><small>Memiliki pembacaan tersimpan</small></div><span class="summary-icon"><x-icon :name="$icon" class="h-6 w-6" /></span></div>
        @endforeach
    </div>
    <div class="section-intro"><div><h3>Pembacaan terakhir</h3><p>Ringkasan saat halaman dibuka. Buka Devices untuk pembaruan otomatis.</p></div><span class="quiet-badge">{{ $current->count() + $tempHumidity->count() + $xyz->count() }} device</span></div>
    @include('partials.sensor-groups', ['live' => false])
@endsection
