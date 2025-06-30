document.addEventListener('livewire:init', function () {
    window.addEventListener('update-employee-daily-expenses-chart', event => {
        const chartId = event.detail.chartId || 'employee-daily-expenses';

        if (window.ApexCharts) {
            window.ApexCharts.exec(chartId, 'updateOptions', {
                yaxis: {
                    title: {
                        text: 'Tutar (₺)',
                    },
                    labels: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('tr-TR', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(val) + ' ₺';
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('tr-TR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(val) + ' ₺';
                        }
                    }
                }
            });
        }
    });
});
