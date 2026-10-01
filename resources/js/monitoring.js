export const fields = {
    current: [{ key: 'current', label: 'Arus', unit: 'A', digits: 2 }],
    temp_humidity: [{ key: 'temperature', label: 'Suhu', unit: '°C', digits: 1 }, { key: 'humidity', label: 'Kelembapan', unit: '%', digits: 1 }],
    xyz: ['x', 'y', 'z'].map(key => ({ key, label: key.toUpperCase(), unit: '', digits: 2 })),
};
export const palette = ['#2563EB', '#111827', '#6B7280'];
export function numeric(value) {
    if (value === null || value === undefined || String(value).trim() === '') return null;
    const number = Number(value);
    return Number.isFinite(number) ? number : null;
}
export function formatNumber(value, digits = 2) {
    const number = numeric(value);
    return number === null ? '—' : number.toLocaleString('id-ID', { minimumFractionDigits: digits, maximumFractionDigits: digits });
}
export function rangeParams(device, from, to) {
    if (!device || !/^\d{4}-\d{2}-\d{2}$/.test(from) || !/^\d{4}-\d{2}-\d{2}$/.test(to) || from > to) {
        throw new Error('Pilih device dan rentang tanggal yang valid. Tanggal akhir tidak boleh mendahului tanggal awal.');
    }
    return new URLSearchParams({ device_id: device, from: `${from} 00:00:00`, to: `${to} 23:59:59.999999` });
}
export function timestamp(value, offset = '+07:00') {
    if (!value) return NaN;
    let text = String(value).replace(' ', 'T');
    if (!/(Z|[+-]\d{2}:?\d{2})$/i.test(text)) text += offset;
    return Date.parse(text);
}
export function appendReading(points, time, values) {
    if (!Number.isFinite(time) || (points.length && time <= points.at(-1).time)) return false;
    points.push({ time, values });
    if (points.length > 30) points.shift();
    return true;
}
export function formatTime(value, timezone = 'Asia/Jakarta') {
    const time = typeof value === 'number' ? value : timestamp(value);
    return Number.isFinite(time) ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'medium', timeZone: timezone }).format(time) : '—';
}
export function relativeTime(value, offset) {
    const time = timestamp(value, offset);
    if (!Number.isFinite(time)) return 'Belum ada waktu pembaruan';
    const seconds = Math.max(0, Math.floor((Date.now() - time) / 1000));
    if (seconds < 5) return 'Diperbarui baru saja';
    if (seconds < 60) return `Diperbarui ${seconds} detik lalu`;
    if (seconds < 3600) return `Diperbarui ${Math.floor(seconds / 60)} menit lalu`;
    if (seconds < 86400) return `Diperbarui ${Math.floor(seconds / 3600)} jam lalu`;
    return `Diperbarui ${Math.floor(seconds / 86400)} hari lalu`;
}
export async function fetchData(url, signal) {
    const response = await fetch(url, { signal, credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'application/json' } });
    if (response.status === 401 || response.status === 419 || response.redirected) throw new Error('Sesi Anda berakhir. Masuk kembali untuk melanjutkan.');
    if (!response.ok) throw new Error('Data belum dapat dimuat. Silakan coba lagi.');
    return response.json();
}
export function lineOptions(height = 320) {
    return {
        chart: { type: 'line', height, fontFamily: 'ui-sans-serif, system-ui, sans-serif', foreColor: '#6B7280', toolbar: { show: false }, animations: { enabled: false }, zoom: { enabled: false }, parentHeightOffset: 0 },
        colors: palette, series: [], stroke: { width: [2.5, 2, 2], curve: 'straight', dashArray: [0, 5, 2] },
        fill: { type: 'solid', opacity: 1 }, dataLabels: { enabled: false }, markers: { size: 0, hover: { size: 4 } },
        grid: { borderColor: '#E5E7EB', strokeDashArray: 4 }, legend: { show: false },
        xaxis: { type: 'datetime', labels: { datetimeUTC: false }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { formatter: value => formatNumber(value) } },
        tooltip: { theme: 'light', x: { formatter: value => formatTime(value) } },
        noData: { text: 'Tidak ada data pada rentang ini.' },
    };
}
