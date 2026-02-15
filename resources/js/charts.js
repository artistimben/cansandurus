/**
 * Chart.js Configuration & Helper Functions
 * Endüstriyel Tema ile Grafik Utilities
 */

import Chart from 'chart.js/auto';

// Global Chart.js konfigürasyonu
Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.color = '#374151'; // gray-700
Chart.defaults.borderColor = '#e5e7eb'; // gray-200

// Endüstriyel renk paleti
export const ChartColors = {
    primary: '#1e3a8a',      // Koyu mavi
    secondary: '#ea580c',    // Turuncu  
    success: '#16a34a',      // Yeşil
    warning: '#eab308',      // Sarı
    danger: '#dc2626',       // Kırmızı
    info: '#0891b2',         // Cyan
    gray: '#6b7280',         // Gray

    // Severity renkleri
    critical: '#dc2626',     // Kırmızı
    high: '#ea580c',         // Turuncu
    medium: '#eab308',       // Sarı
    low: '#16a34a',          // Yeşil

    // Gradient colors (array)
    machineGradient: [
        '#1e3a8a', '#3b82f6', '#60a5fa', // Mavi tonları
        '#ea580c', '#f97316', '#fb923c', // Turuncu tonları
        '#16a34a', '#22c55e', '#4ade80', // Yeşil tonları
    ],
};

// Tooltip ayarları (factory)
export function createTooltipConfig() {
    return {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        titleColor: '#fff',
        bodyColor: '#fff',
        borderColor: '#374151',
        borderWidth: 1,
        padding: 12,
        displayColors: true,
        boxWidth: 12,
        usePointStyle: true,
    };
}

// Responsive konfigürasyon
export const responsiveConfig = {
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 2,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                padding: 15,
                usePointStyle: true,
                font: {
                    size: 11,
                    weight: '500',
                }
            }
        },
        tooltip: createTooltipConfig(),
    },
};

// Helper: Dakikayı saat:dakika formatına çevir
export function formatDuration(minutes) {
    if (!minutes || minutes === 0) return '0 dk';
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;

    if (hours === 0) return `${mins} dk`;
    if (mins === 0) return `${hours} saat`;
    return `${hours}s ${mins}dk`;
}

// Helper: Yüzde hesaplama
export function calculatePercentage(value, total) {
    if (!total || total === 0) return 0;
    return Math.round((value / total) * 100);
}

// Pie/Doughnut Chart oluştur
export function createPieChart(canvasId, labels, data, title = '') {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ChartColors.machineGradient,
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            ...responsiveConfig,
            aspectRatio: 1.5,
            plugins: {
                ...responsiveConfig.plugins,
                title: {
                    display: !!title,
                    text: title,
                    font: {
                        size: 14,
                        weight: 'bold',
                    },
                    padding: 20,
                },
                tooltip: {
                    ...createTooltipConfig(),
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = calculatePercentage(value, total);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Bar Chart oluştur
export function createBarChart(canvasId, labels, datasets, title = '') {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets.map((ds, index) => ({
                label: ds.label,
                data: ds.data,
                backgroundColor: ds.color || ChartColors.machineGradient[index % ChartColors.machineGradient.length],
                borderWidth: 0,
                borderRadius: 4,
            }))
        },
        options: {
            ...responsiveConfig,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6',
                    },
                },
                x: {
                    grid: {
                        display: false,
                    }
                }
            },
            plugins: {
                ...responsiveConfig.plugins,
                title: {
                    display: !!title,
                    text: title,
                    font: {
                        size: 14,
                        weight: 'bold',
                    },
                    padding: 20,
                }
            }
        }
    });
}

// Line Chart oluştur
export function createLineChart(canvasId, labels, datasets, title = '') {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets.map((ds, index) => ({
                label: ds.label,
                data: ds.data,
                borderColor: ds.color || ChartColors.machineGradient[index % ChartColors.machineGradient.length],
                backgroundColor: (ds.color || ChartColors.machineGradient[index % ChartColors.machineGradient.length]) + '20',
                borderWidth: 3,
                fill: ds.fill !== false,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
            }))
        },
        options: {
            ...responsiveConfig,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6',
                    },
                },
                x: {
                    grid: {
                        display: false,
                    }
                }
            },
            plugins: {
                ...responsiveConfig.plugins,
                title: {
                    display: !!title,
                    text: title,
                    font: {
                        size: 14,
                        weight: 'bold',
                    },
                    padding: 20,
                }
            }
        }
    });
}

// Window'a export et (global kullanım için)
window.ChartHelpers = {
    createPieChart,
    createBarChart,
    createLineChart,
    ChartColors,
    formatDuration,
    calculatePercentage,
};
