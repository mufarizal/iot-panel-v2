@php
    $groups = [
        'current' => ['title' => 'Arus', 'description' => 'Pantau pemakaian arus perangkat.', 'items' => $current, 'fields' => ['current' => ['Arus', 'A', 2]]],
        'temp_humidity' => ['title' => 'Suhu & Kelembapan', 'description' => 'Lihat kondisi lingkungan perangkat.', 'items' => $tempHumidity, 'fields' => ['temperature' => ['Suhu', '°C', 1], 'humidity' => ['Kelembapan', '%', 1]]],
        'xyz' => ['title' => 'X / Y / Z', 'description' => 'Pantau perubahan ketiga parameter.', 'items' => $xyz, 'fields' => ['x' => ['X', '', 2], 'y' => ['Y', '', 2], 'z' => ['Z', '', 2]]],
    ];
@endphp
<div class="sensor-sections {{ $live ? 'live-sections' : 'snapshot-sections' }}">
    @foreach($groups as $type => $group)
        <section data-device-group="{{ $type }}" aria-labelledby="group-{{ $type }}">
            <div class="section-heading"><div class="flex items-center gap-3"><span class="section-icon"><x-icon :name="$type" /></span><div><h3 id="group-{{ $type }}">{{ $group['title'] }} <span class="count-badge">{{ $group['items']->count() }}</span></h3><p>{{ $group['description'] }}</p></div></div></div>
            <div class="device-grid">
                @forelse($group['items'] as $item)
                    <article class="panel device-card" data-device-id="{{ $item->device_id }}" data-device-type="{{ $type }}" data-recorded-at="{{ $item->recorded_at }}">
                        <div class="device-card-heading"><div><p class="eyebrow">{{ $group['title'] }}</p><h4>{{ $item->name ?? 'Device '.$item->serial_number }}</h4></div><span class="device-symbol"><x-icon :name="$type" /></span></div>
                        @if($live)
                            <div class="device-chart" data-device-chart role="img" aria-label="{{ $type === 'xyz' ? 'Grafik perubahan X, Y, dan Z' : 'Indikator '.$group['title'] }}"></div>
                        @endif
                        <dl class="reading-grid" style="--reading-columns: {{ count($group['fields']) }}">
                            @foreach($group['fields'] as $field => [$label, $unit, $digits])
                                <div><dt><span class="series-dot series-{{ $loop->index }}"></span>{{ $label }}</dt><dd><span data-field="{{ $field }}" data-value="{{ $item->{$field} }}">{{ $item->{$field} === null ? '—' : number_format($item->{$field}, $digits, ',', '.') }}</span>@if($unit)<span class="reading-unit">{{ $unit }}</span>@endif</dd></div>
                            @endforeach
                        </dl>
                        @if($live)
                            <p class="chart-caption" data-scale-label>{{ $type === 'current' ? 'Skala 0–10 A · menyesuaikan pembacaan' : ($type === 'temp_humidity' ? 'Suhu 0–50 °C · Kelembapan 0–100%' : 'Maks. 30 pembacaan terbaru selama halaman terbuka') }}</p>
                        @endif
                        <div class="device-card-footer"><span class="status-dot neutral"></span><time data-field="recorded_at" datetime="{{ $item->recorded_at }}">Diperbarui {{ \Carbon\Carbon::parse($item->recorded_at)->locale('id')->diffForHumans() }}</time></div>
                        @if($live)<div class="device-detail"><span>Waktu pembacaan terakhir</span><strong data-detail-time>{{ \Carbon\Carbon::parse($item->recorded_at)->locale('id')->translatedFormat('d M Y, H:i:s') }}</strong><a class="text-link" href="{{ route('history', ['device_id' => $item->device_id]) }}">Lihat riwayat device <x-icon name="arrow" class="h-4 w-4" /></a></div>@endif
                    </article>
                @empty
                    <div class="panel empty-state"><x-icon :name="$type" class="h-7 w-7" /><h4>Belum ada pembacaan</h4><p>Data perangkat akan tampil setelah pembacaan pertama diterima.</p></div>
                @endforelse
            </div>
        </section>
    @endforeach
</div>
