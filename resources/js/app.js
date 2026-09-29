/**
 * Smart Health Advisor - Main Application JavaScript
 */

import './bootstrap';
import './assessment';
import './video-recorder';
import './charts';

// ===== Global Utilities =====

/**
 * Show a toast notification
 */
window.showToast = function (message, type = 'info') {
    const container = document.getElementById('toastContainer') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show`;
    toast.style.minWidth = '300px';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    container.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 5000);
};

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
    document.body.appendChild(container);
    return container;
}

/**
 * Confirm action helper
 */
window.confirmAction = function (message = 'Are you sure?') {
    return confirm(message);
};

/**
 * Debounce function
 */
window.debounce = function (func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

/**
 * Format date
 */
window.formatDate = function (date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// ===== CSRF Setup for AJAX =====
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
}

// ===== Auto-dismiss alerts =====
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert:not(.alert-permanent)').forEach((alert) => {
        if (!alert.classList.contains('alert-dismissible')) return;
        setTimeout(() => {
            if (alert.parentNode) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    });

    // Add fade-in animation to cards
    document.querySelectorAll('.card, .stat-card').forEach((el) => {
        el.classList.add('fade-in');
    });
});