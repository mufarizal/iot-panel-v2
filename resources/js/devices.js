import { fields, palette, numeric, formatNumber, timestamp, relativeTime, formatTime, appendReading, fetchData, lineOptions } from './monitoring';

export async function initDevices(root) {
    const registry = new Map();
    const selector = root.querySelector('#device-selector');
    const status = root.querySelector('[data-poll-status]');
    const chartError = root.querySelector('[data-chart-error]');
    const offset = root.dataset.timezoneOffset;
    const timezone = root.dataset.timezone;
    let polling = false;

    for (const card of root.querySelectorAll('[data-device-id]')) {
        const type = card.dataset.deviceType;
        const state = { card, type, chart: null, points: [], recordedAt: card.dataset.recordedAt, max: 10, values: fields[type].map(field => numeric(card.querySelector(`[data-field="${field.key}"]`).dataset.value)) };
        registry.set(card.dataset.deviceId, state);
        appendReading(state.points, timestamp(state.recordedAt, offset), state.values);
        if (!window.ApexCharts) continue;
        try {
            let options;
            if (type === 'xyz') {
                options = lineOptions(150);
                options.chart.sparkline = { enabled: true };
                options.series = xyzSeries(state);
                options.markers = { size: state.points.length === 1 ? 3 : 0 };
            } else {
                const labels = fields[type].map(field => field.label);
                options = {
                    chart: { type: 'radialBar', height: 205, parentHeightOffset: 0, fontFamily: 'ui-sans-serif, system-ui, sans-serif', animations: { enabled: false }, sparkline: { enabled: true } },
                    series: gaugeSeries(state), labels, colors: palette,
                    plotOptions: { radialBar: {
                        startAngle: -125, endAngle: 125, hollow: { size: type === 'current' ? '66%' : '48%' },
                        track: { background: '#F3F4F6', strokeWidth: '100%', margin: 8 },
                        dataLabels: { name: { show: type === 'current', color: '#6B7280', fontSize: '11px', offsetY: 22 }, value: { show: type === 'current', offsetY: -12, fontSize: '28px', fontWeight: 600, color: '#111827', formatter: () => `${formatNumber(state.values[0])} A` }, total: { show: type !== 'current', label: 'Lingkungan', fontSize: '11px', fontWeight: 500, color: '#6B7280', formatter: () => '' } },
                    } },
                    fill: { type: 'solid', opacity: 1 }, stroke: { lineCap: 'round' },
                    tooltip: { enabled: false },
                };
                if (type !== 'current') {
                    options.plotOptions.radialBar.dataLabels.name = { show: true, color: '#6B7280', fontSize: '10px', offsetY: 4 };
                }
            }
            state.chart = new window.ApexCharts(card.querySelector('[data-device-chart]'), options);
            await state.chart.render();
        } catch {
            state.chart = null;
            chartError.hidden = false;
        }
    }
    if (!window.ApexCharts && registry.size) chartError.hidden = false;

    function xyzSeries(state) {
        return fields.xyz.map((field, index) => ({ name: field.label, data: state.points.map(point => ({ x: point.time, y: point.values[index] })) }));
    }
    function gaugeSeries(state) {
        if (state.type === 'current') {
            state.max = Math.max(state.max, Math.ceil(Math.abs(state.values[0] ?? 0) / 5) * 5);
            state.card.querySelector('[data-scale-label]').textContent = `Skala 0–${formatNumber(state.max, 0)} A · menyesuaikan pembacaan`;
            return [Math.min(100, Math.max(0, (state.values[0] ?? 0) / state.max * 100))];
        }
        return state.values.map((value, index) => Math.min(100, Math.max(0, (value ?? 0) / (index === 0 ? 50 : 100) * 100)));
    }
    function refreshTimes() {
        for (const state of registry.values()) {
            const element = state.card.querySelector('[data-field="recorded_at"]');
            element.textContent = relativeTime(state.recordedAt, offset);
            element.dateTime = state.recordedAt;
            state.card.querySelector('[data-detail-time]').textContent = formatTime(timestamp(state.recordedAt, offset), timezone);
        }
    }
    async function applySelection() {
        const selected = selector.value;
        const focused = selected !== 'all';
        root.classList.toggle('is-focused', focused);
        for (const section of root.querySelectorAll('[data-device-group]')) {
            const cards = [...section.querySelectorAll('[data-device-id]')];
            cards.forEach(card => { card.hidden = focused && card.dataset.deviceId !== selected; });
            section.hidden = focused && cards.every(card => card.hidden);
        }
        root.querySelector('[data-visible-count]').textContent = focused ? '1 device' : `${registry.size} device`;
        for (const state of registry.values()) {
            if (state.card.hidden || !state.chart) continue;
            try {
                await state.chart.updateOptions({ chart: { height: focused ? 310 : state.type === 'xyz' ? 150 : 205 } }, false, false);
            } catch { chartError.hidden = false; }
        }
    }
    selector.addEventListener('change', applySelection);
    refreshTimes();
    async function poll() {
        refreshTimes();
        if (polling || document.hidden) return;
        polling = true;
        try {
            const data = await fetchData(root.dataset.endpoint, AbortSignal.timeout(12000));
            for (const [type, readings] of Object.entries(data)) {
                if (!fields[type] || !Array.isArray(readings)) continue;
                for (const reading of readings) {
                    const state = registry.get(String(reading.device_id));
                    if (!state || state.type !== type) continue;
                    const time = timestamp(reading.recorded_at, offset);
                    if (time < timestamp(state.recordedAt, offset)) continue;
                    state.recordedAt = reading.recorded_at;
                    state.values = fields[type].map(field => numeric(reading[field.key]));
                    fields[type].forEach((field, index) => {
                        state.card.querySelector(`[data-field="${field.key}"]`).textContent = formatNumber(state.values[index], field.digits);
                    });
                    const fresh = appendReading(state.points, time, state.values);
                    if (state.chart && (type !== 'xyz' || fresh)) {
                        try {
                            await state.chart.updateSeries(type === 'xyz' ? xyzSeries(state) : gaugeSeries(state), false);
                        } catch { chartError.hidden = false; }
                    }
                }
            }
            refreshTimes();
            status.hidden = true;
        } catch (error) {
            status.textContent = error.message.includes('Sesi') ? error.message : 'Pembaruan sementara terhenti. Nilai terakhir tetap ditampilkan; kami akan mencoba lagi otomatis.';
            status.hidden = false;
        } finally { polling = false; }
    }
    let timer = window.setInterval(poll, 3000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) poll(); });
    window.addEventListener('pagehide', () => window.clearInterval(timer));
    window.addEventListener('pageshow', event => {
        if (event.persisted) { window.clearInterval(timer); timer = window.setInterval(poll, 3000); poll(); }
    });
}
