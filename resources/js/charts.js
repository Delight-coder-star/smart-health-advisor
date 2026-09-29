/**
 * Charts & Data Visualization
 * Reusable chart initialization functions
 */

window.SHACharts = {
    /**
     * Create a line chart for progress tracking
     */
    createProgressChart(canvasId, labels, datasets) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        return new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets.map((ds, i) => ({
                    label: ds.label,
                    data: ds.data,
                    borderColor: ds.color || this.getDefaultColor(i),
                    backgroundColor: this.hexToRgba(ds.color || this.getDefaultColor(i), 0.1),
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: ds.color || this.getDefaultColor(i),
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                })),
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 16 },
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: (value) => value + '%', color: '#6b7280' },
                        grid: { color: '#f3f4f6' },
                    },
                    x: {
                        ticks: { color: '#6b7280' },
                        grid: { display: false },
                    },
                },
            },
        });
    },

    /**
     * Create a doughnut chart for distribution
     */
    createDoughnutChart(canvasId, labels, data, colors) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        return new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors || ['#10b981', '#f59e0b', '#f97316', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 16 },
                    },
                },
            },
        });
    },

    /**
     * Create a bar chart
     */
    createBarChart(canvasId, labels, data, label = 'Count', color = '#4f46e5') {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        return new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: this.hexToRgba(color, 0.8),
                    borderRadius: 8,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#6b7280' },
                        grid: { color: '#f3f4f6' },
                    },
                    x: {
                        ticks: { color: '#6b7280' },
                        grid: { display: false },
                    },
                },
            },
        });
    },

    /**
     * Create a radar chart for multi-dimensional risk
     */
    createRadarChart(canvasId, labels, data) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        return new Chart(canvas.getContext('2d'), {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Your Scores',
                    data: data,
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: '#4f46e5',
                    borderWidth: 2,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: { stepSize: 1, display: false },
                        grid: { color: '#e5e7eb' },
                        angleLines: { color: '#e5e7eb' },
                        pointLabels: { font: { size: 12, weight: '500' }, color: '#4b5563' },
                    },
                },
            },
        });
    },

    /**
     * Get default color by index
     */
    getDefaultColor(index) {
        const colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#7c3aed', '#3b82f6'];
        return colors[index % colors.length];
    },

    /**
     * Convert hex to rgba
     */
    hexToRgba(hex, alpha = 1) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    },
};