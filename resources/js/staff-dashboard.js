import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    BarController,
    BarElement,
    ArcElement,
    DoughnutController,
    Filler,
    Tooltip,
    Legend,
} from 'chart.js';

Chart.register(
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    BarController,
    BarElement,
    ArcElement,
    DoughnutController,
    Filler,
    Tooltip,
    Legend,
);

const brand = {
    gold: '#785900',
    goldLight: '#ffc107',
    sky: '#006399',
    skyLight: '#04a8ff',
    green: '#006e1c',
    grid: '#e2e8f0',
};

function readDashboardData() {
    const node = document.getElementById('staff-dashboard-data');

    if (!node) {
        return null;
    }

    try {
        return JSON.parse(node.textContent);
    } catch {
        return null;
    }
}

function baseOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    font: { family: 'Quicksand, sans-serif', weight: '600' },
                    color: '#475569',
                },
            },
        },
    };
}

function renderLineChart(canvas, chartData) {
    return new Chart(canvas, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Submissions',
                data: chartData.values,
                borderColor: brand.sky,
                backgroundColor: 'rgba(4, 168, 255, 0.12)',
                fill: true,
                tension: 0.35,
                pointBackgroundColor: brand.goldLight,
                pointBorderColor: brand.gold,
                pointRadius: 4,
            }],
        },
        options: {
            ...baseOptions(),
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', maxRotation: 0, autoSkip: true, maxTicksLimit: 7 },
                },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: '#64748b' },
                    grid: { color: brand.grid },
                },
            },
            plugins: {
                ...baseOptions().plugins,
                legend: { display: false },
            },
        },
    });
}

function renderDoughnutChart(canvas, chartData) {
    return new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.values,
                backgroundColor: chartData.colors,
                borderWidth: 2,
                borderColor: '#ffffff',
            }],
        },
        options: {
            ...baseOptions(),
            cutout: '62%',
            plugins: {
                ...baseOptions().plugins,
                legend: { position: 'bottom' },
            },
        },
    });
}

function renderBarChart(canvas, chartData) {
    return new Chart(canvas, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Attempts',
                data: chartData.values,
                backgroundColor: chartData.colors ?? [brand.gold],
                borderRadius: 8,
            }],
        },
        options: {
            ...baseOptions(),
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b' },
                },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: '#64748b' },
                    grid: { color: brand.grid },
                },
            },
            plugins: {
                ...baseOptions().plugins,
                legend: { display: false },
            },
        },
    });
}

function initStaffDashboard() {
    const payload = readDashboardData();

    if (!payload?.charts) {
        return;
    }

    const charts = payload.charts;
    const instances = [];

    document.querySelectorAll('[data-chart-id]').forEach((canvas) => {
        const id = canvas.dataset.chartId;
        const type = canvas.dataset.chartType;

        if (id === 'admin-submissions-chart' && charts.submissions_over_time) {
            instances.push(renderLineChart(canvas, charts.submissions_over_time));
        }

        if (id === 'teacher-workload-chart' && charts.workload_trend) {
            instances.push(renderLineChart(canvas, charts.workload_trend));
        }

        if ((id === 'admin-status-chart' || id === 'teacher-status-chart') && charts.grading_status) {
            instances.push(renderDoughnutChart(canvas, charts.grading_status));
        }

        if (id === 'admin-weeks-chart' && charts.attempts_by_week) {
            instances.push(renderBarChart(canvas, charts.attempts_by_week));
        }
    });

    window.addEventListener('beforeunload', () => {
        instances.forEach((chart) => chart.destroy());
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStaffDashboard);
} else {
    initStaffDashboard();
}
