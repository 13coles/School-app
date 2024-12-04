document.addEventListener('DOMContentLoaded', function() {
    function createResponsiveChart(elementId, chartType, labels, datasets, options = {}) {
        const ctx = document.getElementById(elementId).getContext('2d');
        
        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: chartType !== 'pie'
                }
            }
        };

        const mergedOptions = {...defaultOptions, ...options};

        return new Chart(ctx, {
            type: chartType,
            data: {
                labels: labels,
                datasets: datasets
            },
            options: mergedOptions
        });
    }

    // Bar Chart for Student Records
    createResponsiveChart('visitors-chart', 'bar', 
        ['Math', 'Science', 'English', 'History', 'Computer'],
        [
            {
                label: 'Passed',
                data: [45, 38, 52, 40, 55],
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            },
            {
                label: 'Failed',
                data: [5, 12, 3, 10, 2],
                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1
            }
        ],
        {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Students'
                    }
                }
            }
        }
    );

    // Pie Charts for Performance by section
    function createPieChart(elementId, passedPercentage) {
        createResponsiveChart(elementId, 'pie', 
            ['Passed', 'Failed'], 
            [{
                data: [passedPercentage, 100 - passedPercentage],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',   // Green for Passed
                    'rgba(220, 53, 69, 0.8)'    // Red for Failed
                ]
            }],
            {
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.formattedValue + '%';
                            }
                        }
                    }
                }
            }
        );
    }

    // Create pie charts for each section
    createPieChart('charity', 65);
    createPieChart('humility', 72);
});