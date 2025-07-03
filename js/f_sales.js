// Sample data for airlines and ticket sales
const airlineLabels = [
    'AirAsia', 'Malaysia Airline', 'Batix Air', 'Qatar Airline', 'Singapore Airline',
    'Firefly', 'MASwings', 'Malindo', 'Emirates', 'Scoot', 'Turkish Airline'
];
const ticketSales = [
    65000, 33000, 25000, 53000, 42000,
    29000, 36000, 44000, 15000, 74000, 26000, 48000
];

const ctx = document.getElementById('salesBarChart').getContext('2d');
const salesBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: airlineLabels,
        datasets: [{
            label: 'Tickets Sold',
            data: ticketSales,
            backgroundColor: '#5286F8',
            borderWidth: 1,
            borderRadius: 8,
            barPercentage: 0.7,
            categoryPercentage: 0.7
        }]
    },
    options: {
        responsive: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                ticks: {
                    color: '#2B3034',
                    font: { family: 'Poppins', size: 13 },
                    maxRotation: 45,
                    minRotation: 45
                },
                grid: { display: false }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#2B3034',
                    font: { family: 'Poppins', size: 13 },
                    callback: function(value) { return value >= 1000 ? (value/1000) + 'k' : value; }
                },
                grid: {
                    color: '#F0F4FA',
                    borderDash: [4, 4]
                },
                suggestedMax: 80000
            }
        }
    }
});
