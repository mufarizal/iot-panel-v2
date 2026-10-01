import { fields, numeric, formatNumber, rangeParams, timestamp, formatTime, fetchData, lineOptions } from './monitoring';

export function initHistory(root) {
    const form = root.querySelector('#history-filter');
    const device = form.elements.device_id;
    const from = form.elements.from;
    const to = form.elements.to;
    const interval = form.elements.interval;
    const download = root.querySelector('#download-excel');
    const message = root.querySelector('#filter-message');
    const exportOnly = root.dataset.exportOnly === 'true';
    const resultStatus = root.querySelector('#history-status');
    const tableView = root.querySelector('#table-view');
    const chartView = root.querySelector('#chart-view');
    const summary = root.querySelector('#result-summary');
    const previous = root.querySelector('#previous-page');
    const next = root.querySelector('#next-page');
    let mode = 'table';
    let applied = null;
    let currentPage = 1;
    let lastPage = 1;
    let chart = null;
    let controller = null;
    let generation = 0;
    let chartQueue = Promise.resolve();

    function selection() {
        const params = rangeParams(device.value, from.value, to.value);
        const type = device.selectedOptions[0]?.dataset.type;
        if (!fields[type]) throw new Error('Pilih device yang tersedia untuk melanjutkan.');
        return { params, type, name: device.selectedOptions[0].textContent, from: from.value, to: to.value, interval: interval.value };
    }
    function updateDownload(showError = false) {
        to.min = from.value;
        to.setCustomValidity(to.value && from.value && to.value < from.value ? 'Tanggal akhir tidak boleh mendahului tanggal awal.' : '');
        try {
            const selected = selection();
            download.href = `${root.dataset.exportEndpoint}?${selected.params}`;
            download.setAttribute('aria-disabled', 'false');
            download.removeAttribute('tabindex');
            if (showError) message.hidden = true;
            return true;
        } catch (error) {
            download.removeAttribute('href');
            download.setAttribute('aria-disabled', 'true');
            download.setAttribute('tabindex', '-1');
            if (showError) { message.textContent = error.message; message.hidden = false; }
            return false;
        }
    }
    form.addEventListener('input', () => updateDownload(true));
    form.addEventListener('change', () => updateDownload(true));
    download.addEventListener('click', event => {
        if (!updateDownload(true) || !form.reportValidity()) event.preventDefault();
    });
    updateDownload();
    if (exportOnly) {
        form.addEventListener('submit', event => { event.preventDefault(); if (updateDownload(true) && form.reportValidity()) download.click(); });
        return;
    }

    function showStatus(text) {
        resultStatus.replaceChildren();
        const paragraph = document.createElement('p');
        paragraph.textContent = text;
        resultStatus.append(paragraph);
        resultStatus.hidden = false;
        tableView.hidden = true;
        chartView.hidden = true;
    }
    function cancelRequest() {
        generation++;
        controller?.abort();
        root.querySelector('.history-results').setAttribute('aria-busy', 'false');
    }
    form.addEventListener('submit', event => {
        event.preventDefault();
        if (!form.reportValidity()) return;
        try {
            applied = selection();
            message.hidden = true;
            load(1);
        } catch (error) { message.textContent = error.message; message.hidden = false; }
    });
    root.querySelectorAll('[data-history-mode]').forEach(button => {
        button.addEventListener('click', () => {
            if (mode === button.dataset.historyMode) return;
            cancelRequest();
            mode = button.dataset.historyMode;
            root.querySelectorAll('[data-history-mode]').forEach(tab => tab.setAttribute('aria-pressed', String(tab === button)));
            root.querySelector('#interval-field').hidden = mode !== 'chart';
            // A mode change is a manual action; it uses the last applied date/device filters.
            if (applied) { applied = { ...applied, interval: interval.value }; load(1); }
            else showStatus('Pilih filter, lalu klik Terapkan Filter untuk melihat pembacaan.');
        });
    });
    previous.addEventListener('click', () => { if (currentPage > 1) load(currentPage - 1); });
    next.addEventListener('click', () => { if (currentPage < lastPage) load(currentPage + 1); });

    async function load(page) {
        cancelRequest();
        controller = new AbortController();
        const signal = AbortSignal.any([controller.signal, AbortSignal.timeout(20000)]);
        const requestId = generation;
        const selected = { ...applied };
        const selectedMode = mode;
        const params = new URLSearchParams(selected.params);
        if (selectedMode === 'chart') params.set('interval', selected.interval);
        else params.set('page', page);
        showStatus('Memuat pembacaan…');
        summary.textContent = `${selected.name} · ${selected.from} — ${selected.to}`;
        root.querySelector('.history-results').setAttribute('aria-busy', 'true');
        previous.disabled = next.disabled = true;
        try {
            const endpoint = selectedMode === 'table' ? root.dataset.tableEndpoint : root.dataset.chartEndpoint;
            const data = await fetchData(`${endpoint}?${params}`, signal);
            if (requestId !== generation) return;
            const rows = selectedMode === 'table' ? data.data : data;
            if (!Array.isArray(rows)) throw new Error('Data belum dapat dimuat. Silakan coba lagi.');
            if (!rows.length) {
                showStatus('Tidak ada data pada rentang ini. Coba pilih tanggal atau device lain.');
                return;
            }
            if (selectedMode === 'table') renderTable(data, selected);
            else {
                chartQueue = chartQueue.catch(() => {}).then(async () => {
                    if (requestId !== generation) return;
                    await renderChart(rows, selected);
                });
                await chartQueue;
            }
        } catch (error) {
            if (requestId !== generation) return;
            showStatus(error.name === 'TimeoutError' ? 'Pemuatan terlalu lama. Coba rentang tanggal lebih pendek, lalu terapkan filter kembali.' : error.message || 'Data belum dapat dimuat. Silakan coba lagi.');
        } finally {
            if (requestId === generation) root.querySelector('.history-results').setAttribute('aria-busy', 'false');
        }
    }
    function renderTable(data, selected) {
        const columns = fields[selected.type];
        const head = root.querySelector('#history-table-head');
        const body = root.querySelector('#history-table-body');
        const heading = document.createElement('tr');
        ['Waktu pembacaan', ...columns.map(field => `${field.label}${field.unit ? ` (${field.unit})` : ''}`)].forEach(label => {
            const th = document.createElement('th'); th.scope = 'col'; th.textContent = label; heading.append(th);
        });
        head.replaceChildren(heading);
        const fragment = document.createDocumentFragment();
        for (const row of data.data) {
            const tr = document.createElement('tr');
            const values = [formatTime(timestamp(row.recorded_at, root.dataset.timezoneOffset), root.dataset.timezone), ...columns.map(field => formatNumber(row[field.key], field.digits))];
            values.forEach(value => { const td = document.createElement('td'); td.textContent = value; tr.append(td); });
            fragment.append(tr);
        }
        body.replaceChildren(fragment);
        currentPage = Number(data.current_page);
        lastPage = Number(data.last_page);
        root.querySelector('#pagination-summary').textContent = `${data.from ?? ((currentPage - 1) * data.per_page + 1)}–${data.to ?? ((currentPage - 1) * data.per_page + data.data.length)} dari ${formatNumber(data.total, 0)} pembacaan · Halaman ${currentPage} dari ${lastPage}`;
        previous.disabled = currentPage <= 1;
        next.disabled = currentPage >= lastPage;
        resultStatus.hidden = true;
        chartView.hidden = true;
        tableView.hidden = false;
    }
    async function renderChart(rows, selected) {
        if (!window.ApexCharts) throw new Error('Grafik belum dapat dimuat. Muat ulang halaman atau gunakan tampilan Tabel.');
        const columns = fields[selected.type];
        const series = columns.map(field => ({ name: field.label, data: rows.map(row => ({ x: timestamp(row.bucket, root.dataset.timezoneOffset), y: numeric(row[field.key]) })).filter(point => Number.isFinite(point.x)) }));
        const options = lineOptions(350);
        options.series = series;
        options.markers = { size: rows.length === 1 ? 4 : 0 };
        options.xaxis.labels.formatter = (value, time) => new Intl.DateTimeFormat('id-ID', { timeZone: root.dataset.timezone, ...(selected.interval === 'day' ? { day: '2-digit', month: 'short' } : { hour: '2-digit', minute: '2-digit' }) }).format(new Date(time));
        options.tooltip.x.formatter = value => formatTime(value, root.dataset.timezone);
        options.tooltip.y = { formatter: (value, { seriesIndex }) => `${formatNumber(value, columns[seriesIndex].digits)} ${columns[seriesIndex].unit}`.trim() };
        if (selected.type === 'temp_humidity') {
            options.yaxis = columns.map((field, index) => ({ seriesName: field.label, opposite: index === 1, title: { text: `${field.label} (${field.unit})`, style: { fontWeight: 500, color: '#6B7280' } }, labels: { formatter: value => formatNumber(value, 1) } }));
        } else options.yaxis = [{ labels: { formatter: value => formatNumber(value) }, title: { text: selected.type === 'current' ? 'Arus (A)' : 'Nilai', style: { color: '#6B7280', fontWeight: 500 } } }];
        const legend = root.querySelector('#history-chart-legend');
        legend.replaceChildren();
        columns.forEach((field, index) => {
            const label = document.createElement('span'); const dot = document.createElement('i');
            dot.className = `series-dot series-${index}`;
            label.append(dot, document.createTextNode(`${field.label}${field.unit ? ` (${field.unit})` : ''}`)); legend.append(label);
        });
        root.querySelector('#chart-note').textContent = `Data ditampilkan sebagai rata-rata per ${{ minute: 'menit', hour: 'jam', day: 'hari' }[selected.interval]}, bukan setiap pembacaan.`;
        resultStatus.hidden = true;
        tableView.hidden = true;
        chartView.hidden = false;
        if (chart) await chart.updateOptions(options, false, false);
        else { chart = new window.ApexCharts(root.querySelector('#history-chart'), options); await chart.render(); }
    }
}
