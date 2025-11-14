<?php
/**
 * =====================================================
 * CHART COMPONENT - Biểu đồ doanh thu
 * =====================================================
 * File: views/admin/dashboard/cards/chart.php
 * Mô tả: Component biểu đồ doanh thu theo tháng sử dụng Chart.js
 * =====================================================
 */

// Chuẩn bị dữ liệu cho chart
$chartLabels = $monthlyRevenue['labels'] ?? [];
$chartData = $monthlyRevenue['data'] ?? [];

// Convert sang JSON để dùng trong JavaScript
$chartLabelsJson = json_encode($chartLabels);
$chartDataJson = json_encode($chartData);
?>

<canvas id="revenueChart" height="80"></canvas>

<script>
// Revenue Line Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');

// Tạo gradient
const gradient = revenueCtx.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(102, 126, 234, 0.5)');
gradient.addColorStop(1, 'rgba(118, 75, 162, 0.0)');

new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?= $chartLabelsJson ?>,
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: <?= $chartDataJson ?>,
            backgroundColor: gradient,
            borderColor: '#667eea',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#667eea',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverBackgroundColor: '#764ba2',
            pointHoverBorderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: {
                        size: 13,
                        weight: '500'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#667eea',
                borderWidth: 1,
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            label += new Intl.NumberFormat('vi-VN', { 
                                style: 'currency', 
                                currency: 'VND' 
                            }).format(context.parsed.y);
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN', {
                            notation: 'compact',
                            compactDisplay: 'short'
                        }).format(value) + ' đ';
                    },
                    font: {
                        size: 12
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 12
                    }
                },
                grid: {
                    display: false,
                    drawBorder: false
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});
</script>

