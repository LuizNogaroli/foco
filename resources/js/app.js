
import Alpine from 'alpinejs';
import htmx from 'htmx.org';

window.Alpine = Alpine;
window.htmx = htmx;

Alpine.start();

document.addEventListener('htmx:configRequest', (event) => {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        event.detail.headers['X-CSRF-TOKEN'] = token.getAttribute('content');
    }
});
