document.addEventListener('DOMContentLoaded', function() {
    // Check if analytics data is available
    if (!window.analyticsData) return;
    
    const analyticsData = window.analyticsData;
    let currentChart = null;
    
    // Initial chart creation with 7d data
    createChart('7d');
    
    // Add filter button functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get the filter type and update chart
            const filterType = this.textContent.trim();
            if (filterType === '7d' || filterType === '30d') {
                createChart(filterType);
                updateStats(filterType);
                updateDateRange(filterType);
            }
        });
    });
    
    function createChart(period) {
        let chartData, labels, flightData, hotelData;
        
        // Select appropriate data based on period
        switch(period) {
            case '7d':
                chartData = analyticsData.daily.slice(-7);
                labels = chartData.map(item => item.label);
                flightData = chartData.map(item => item.flight_revenue);
                hotelData = chartData.map(item => item.hotel_revenue);
                break;
            case '30d':
                chartData = analyticsData.daily;
                labels = chartData.map(item => item.label);
                flightData = chartData.map(item => item.flight_revenue);
                hotelData = chartData.map(item => item.hotel_revenue);
                break;
            default:
                return;
        }
        
        // Destroy existing chart if it exists
        if (currentChart) {
            currentChart.destroy();
        }
        
        // Get the canvas context
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Create the line chart
        currentChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Flight Revenue',
                        data: flightData,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#3B82F6',
                        pointRadius: period === '30d' ? 2 : 4,
                        pointHoverRadius: period === '30d' ? 4 : 6
                    },
                    {
                        label: 'Hotel Revenue',
                        data: hotelData,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#10B981',
                        pointRadius: period === '30d' ? 2 : 4,
                        pointHoverRadius: period === '30d' ? 4 : 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            color: '#6B7280',
                            font: {
                                family: 'Poppins',
                                size: 12,
                                weight: '500'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#F9FAFB',
                        bodyColor: '#F9FAFB',
                        borderColor: '#374151',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.parsed.y;
                                return `${label}: RM ${value.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: true,
                            color: 'rgba(107, 114, 128, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6B7280',
                            font: {
                                family: 'Poppins',
                                size: period === '30d' ? 9 : 11,
                                weight: '400'
                            },
                            maxRotation: period === '30d' ? 45 : 0,
                            minRotation: 0,
                            maxTicksLimit: period === '30d' ? 8 : undefined
                        },
                        border: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(107, 114, 128, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6B7280',
                            font: {
                                family: 'Poppins',
                                size: 11,
                                weight: '400'
                            },
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'k';
                                }
                                return value;
                            }
                        },
                        border: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        hoverBorderWidth: 2
                    }
                }
            }
        });
    }
    
    function updateStats(period) {
        let chartData, flightTotal, hotelTotal, totalRevenue, periodLabel;
        
        switch(period) {
            case '7d':
                chartData = analyticsData.daily.slice(-7);
                periodLabel = '7 days';
                break;
            case '30d':
                chartData = analyticsData.daily;
                periodLabel = '30 days';
                break;
            default:
                return;
        }
        
        flightTotal = chartData.reduce((sum, item) => sum + item.flight_revenue, 0);
        hotelTotal = chartData.reduce((sum, item) => sum + item.hotel_revenue, 0);
        totalRevenue = flightTotal + hotelTotal;
        
        // Update the stat values in DOM
        const statItems = document.querySelectorAll('.stat-item');
        if (statItems.length >= 3) {
            statItems[0].querySelector('.stat-value').textContent = `RM ${totalRevenue.toLocaleString()}`;
            statItems[0].querySelector('.stat-label').textContent = `Total Revenue (${periodLabel})`;
            statItems[1].querySelector('.stat-value').textContent = `RM ${flightTotal.toLocaleString()}`;
            statItems[2].querySelector('.stat-value').textContent = `RM ${hotelTotal.toLocaleString()}`;
        }
    }
    
    function updateDateRange(period) {
        const dateRangeElement = document.getElementById('dateRange');
        if (!dateRangeElement) return;
        
        const today = new Date();
        let startDate;
        
        switch(period) {
            case '7d':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 6);
                break;
            case '30d':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 29);
                break;
            default:
                return;
        }
        
        const formatDate = (date) => {
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        };
        
        dateRangeElement.textContent = `${formatDate(startDate)} - ${formatDate(today)}`;
    }
});
