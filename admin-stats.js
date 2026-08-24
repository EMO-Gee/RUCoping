
(function(){

    function ready(fn){
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    ready(function(){

        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded — charts will not render.');
            return;
        }

        /* -------------------------
           REPORTS BY MONTH
        ------------------------- */

        const reportsCtx = document.getElementById('reportsByMonth');

        if (reportsCtx){

            const labels = reportsLabels;     // from PHP
            const data = reportsData;         // from PHP

            new Chart(reportsCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Reports',
                        data: data,
                        backgroundColor: 'rgba(183,78,213,0.85)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

        }

        /* -------------------------
           APPOINTMENTS TREND
        ------------------------- */

        const apptCtx = document.getElementById('appointmentsTrend');

        if (apptCtx){

            const labels = [];
            const now = new Date();

            for (let i = 7; i >= 0; i--) {
                const w = new Date(now);
                w.setDate(now.getDate() - i * 7);
                labels.push(w.toLocaleDateString(undefined, { month: 'short', day: 'numeric' }));
            }

            const apptData = [10,12,8,9,11,12,13,12];

            new Chart(apptCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Appointments',
                        data: apptData,
                        borderColor: '#2B6CB0',
                        backgroundColor: 'rgba(43,108,176,0.12)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 2 }
                        }
                    }
                }
            });

        }

        /* -------------------------
           TESTIMONIAL STATUS
        ------------------------- */

        const pieCtx = document.getElementById('testimonialsPie');

        if (pieCtx){

            const approved = testimonialsApproved;
            const pending = testimonialsPending;
            const rejected = testimonialsRejected;

            new Chart(pieCtx, {
                type: 'bar',
                data: {
                    labels: ['Approved','Pending','Rejected'],
                    datasets: [{
                        label: 'Testimonials',
                        data: [approved, pending, rejected],
                        backgroundColor: ['#4CAF50','#FFC107','#B22222']
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

        }

    });

})();
