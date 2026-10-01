import { initDevices } from './devices';
import { initHistory } from './history';

function boot() {
    const toggle = document.querySelector('.mobile-menu');
    toggle?.addEventListener('click', () => {
        const open = document.querySelector('.sidebar').classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && toggle?.getAttribute('aria-expanded') === 'true') toggle.click();
    });
    const devices = document.querySelector('#devices-page');
    const history = document.querySelector('#history-page');
    if (devices) initDevices(devices);
    if (history) initHistory(history);
}
if (document.readyState !== 'complete') document.addEventListener('DOMContentLoaded', boot, { once: true });
else boot();
