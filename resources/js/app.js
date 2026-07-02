import './bootstrap';

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Register PWA Service Worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js');
}

// Simple-DataTables initialization helper
import { DataTable } from 'simple-datatables';
window.initDataTable = (selector, options = {}) => {
    const el = document.querySelector(selector);
    if (el && !el.classList.contains('dataTable-initialized')) {
        el.classList.add('dataTable-initialized');
        return new DataTable(el, {
            searchable: true,
            perPage: 10,
            perPageSelect: [5, 10, 20, 50],
            ...options
        });
    }
};
