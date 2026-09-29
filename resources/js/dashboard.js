/**
 * Dashboard Interactive Scripts
 */

document.addEventListener('DOMContentLoaded', function () {
    // Animate stat cards on load
    document.querySelectorAll('.stat-card').forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, i * 80);
    });

    // Animate progress bars
    document.querySelectorAll('.progress-bar').forEach((bar) => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.transition = 'width 0.8s ease';
            bar.style.width = width;
        }, 200);
    });

    // Auto-refresh alerts (only on advisor/admin dashboards)
    const alertsContainer = document.querySelector('[data-auto-refresh="alerts"]');
    if (alertsContainer) {
        setInterval(() => {
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then((r) => r.text())
                .then((html) => {
                    // Silent refresh — do nothing for now
                })
                .catch(() => {});
        }, 60000);
    }
});