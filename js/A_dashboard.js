document.addEventListener('DOMContentLoaded', function() {
    if (!window.salesData) return;
    const salesData = window.salesData;
    const labels = salesData.map(item => item.name);
    const data = salesData.map(item => Number(item.revenue.replace(/[^\d]/g, '')));
    const chartLabel = 'Revenue (RM)';
    const ctx = document.getElementById('salesBarChart').getContext('2d');
    const salesBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: chartLabel,
                data: data,
                backgroundColor: '#5286F8',
                borderWidth: 1,
                borderRadius: 8,
                barPercentage: 0.7,
                categoryPercentage: 0.7
            }]
        },
        options: {
            responsive: false,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    ticks: { color: '#2B3034', font: { family: 'Poppins', size: 13 }, maxRotation: 45, minRotation: 45 },
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#2B3034',
                        font: { family: 'Poppins', size: 13 },
                        callback: function(value) { return value >= 1000 ? (value/1000) + 'k' : value; }
                    },
                    grid: { color: '#F0F4FA', borderDash: [4, 4] },
                    suggestedMax: 120000
                }
            }
        }
    });

    if (window.salesData2) {
        const salesData2 = window.salesData2;
        const labels2 = salesData2.map(item => item.name);
        const data2 = salesData2.map(item => Number(item.revenue.replace(/[^\d]/g, '')));
        const chartLabel2 = 'Revenue (RM)';
        const ctx2 = document.getElementById('salesBarChart2').getContext('2d');
        const salesBarChart2 = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labels2,
                datasets: [{
                    label: chartLabel2,
                    data: data2,
                    backgroundColor: '#5286F8',
                    borderWidth: 1,
                    borderRadius: 8,
                    barPercentage: 0.7,
                    categoryPercentage: 0.7
                }]
            },
            options: {
                responsive: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        ticks: { color: '#2B3034', font: { family: 'Poppins', size: 13 }, maxRotation: 45, minRotation: 45 },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#2B3034',
                            font: { family: 'Poppins', size: 13 },
                            callback: function(value) { return value >= 1000 ? (value/1000) + 'k' : value; }
                        },
                        grid: { color: '#F0F4FA', borderDash: [4, 4] },
                        suggestedMax: 120000
                    }
                }
            }
        });
    }
});
