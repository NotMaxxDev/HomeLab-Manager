import './bootstrap';
import Alpine from 'alpinejs';
import hljs from 'highlight.js';
import { marked } from 'marked';

window.Alpine = Alpine;
window.marked = marked;
window.hljs = hljs;

// Streaming-Target für Livewire stream() (Chat-Antworten)
window.addEventListener('stream', (event) => {
    const payload = event.detail?.body ?? event.detail;
    if (!payload || payload.type !== 'directive') {
        return;
    }
    const el = document.querySelector(`[wire\\:stream="${payload.name}"]`);
    if (!el) {
        return;
    }
    if (payload.mode === 'replace') {
        el.textContent = payload.content;
    } else {
        el.textContent += payload.content;
    }
    el.scrollTop = el.scrollHeight;
});

Alpine.start();
